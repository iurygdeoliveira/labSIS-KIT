<?php

declare(strict_types=1);

namespace App\Filament\Resources\Media\Tables;

use App\Filament\Resources\Media\Actions\DeleteMediaAction;
use App\Models\MediaItem as MediaItemModel;
use App\Support\AppDateTime;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MediaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('attachment_name')
                    ->label('Nome do Arquivo')
                    ->state(function ($record) {
                        if ((bool) ($record->video ?? false)) {
                            $linkedVideo = $record->linkedVideo();

                            return ($linkedVideo !== null && $linkedVideo->title !== '')
                                ? $linkedVideo->title
                                : 'Vídeo (URL)';
                        }

                        $media = $record->getFirstMedia('media');

                        return $media !== null ? $media->name : '—';
                    })
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(true, fn ($query, $direction) => self::sortAttachmentName($query, $direction)),
                TextColumn::make('file_type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Imagem' => 'primary',
                        'Vídeo' => 'warning',
                        'Documento' => 'success',
                        'Áudio' => 'danger',
                        default => 'secondary',
                    }),
                TextColumn::make('created_at_display')
                    ->label('Criado em')
                    ->state(fn ($record): string => self::resolveCreatedAt($record)),
                TextColumn::make('organization.name')
                    ->label('Organização')
                    ->state(fn ($record): string => self::resolveOrganizationName($record))
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->tooltip('Visualizar')
                        ->color('gray')
                        ->icon(Heroicon::Eye)
                        ->hiddenLabel(),
                    EditAction::make()
                        ->tooltip('Editar')
                        ->color('success')
                        ->icon(Heroicon::PencilSquare)
                        ->hiddenLabel(),
                    DeleteMediaAction::make()
                        ->tooltip('Excluir')
                        ->hiddenLabel(),
                ])
                    ->buttonGroup(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    private static function sortAttachmentName($query, $direction)
    {
        $query
            ->leftJoin('videos', 'videos.media_item_id', '=', 'media_items.id')
            ->leftJoin('media', function ($join): void {
                $join->on('media.model_id', '=', 'media_items.id')
                    ->where('media.model_type', '=', MediaItemModel::class)
                    ->where('media.collection_name', '=', 'media');
            })
            ->orderByRaw(
                'CASE WHEN media_items.video THEN COALESCE(videos.title, "") ELSE COALESCE(media.name, "") END '.($direction === 'desc' ? 'desc' : 'asc')
            )
            ->select('media_items.*');

        return $query;
    }

    private static function resolveCreatedAt($record): string
    {
        if ((bool) ($record->video ?? false)) {
            $createdAt = $record->linkedVideo()?->created_at;

            return $createdAt ? AppDateTime::parse($createdAt)->format('d/m/Y H:i') : '—';
        }

        $media = $record->getFirstMedia('media');

        return $media?->created_at?->format('d/m/Y H:i') ?? '—';
    }

    private static function resolveOrganizationName($record): string
    {
        if (! ($record->organization_id ?? null)) {
            return Filament::hasTenancy() ? '—' : 'Global';
        }

        $organization = $record->organization;

        return $organization !== null ? $organization->name : '—';
    }
}
