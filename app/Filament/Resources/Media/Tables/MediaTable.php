<?php

namespace App\Filament\Resources\Media\Tables;

use App\Filament\Resources\Media\Actions\DeleteMediaAction;
use App\Models\MediaItem as MediaItemModel;
use App\Models\Tenant;
use App\Support\AppDateTime;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
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
                            return $record->video->title ?? 'Vídeo (URL)';
                        }

                        return $record->getFirstMedia('media')->name ?? '—';
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
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Imagem' => 'heroicon-c-photo',
                        'Vídeo' => 'heroicon-c-video-camera',
                        'Documento' => 'heroicon-c-document',
                        'Áudio' => 'heroicon-c-musical-note',
                        default => 'heroicon-c-question-mark-circle',
                    }),
                TextColumn::make('created_at_display')
                    ->label('Criado em')
                    ->state(fn ($record): string => self::resolveCreatedAt($record)),
                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->state(fn ($record): string => self::resolveTenantName($record))
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->icon('heroicon-s-eye')->label('')->tooltip('Visualizar'),
                EditAction::make()->icon('heroicon-s-pencil')->label('')->tooltip('Editar'),
                DeleteMediaAction::make()->icon('heroicon-s-trash')->label('')->tooltip('Excluir'),
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
            $createdAt = $record->video->created_at;

            return $createdAt ? AppDateTime::parse($createdAt)->format('d/m/Y H:i') : '—';
        }

        $media = $record->getFirstMedia('media');

        return $media?->created_at?->format('d/m/Y H:i') ?? '—';
    }

    private static function resolveTenantName($record): string
    {
        $tenantId = $record->tenant_id ?? null;
        if (! $tenantId) {
            return Filament::hasTenancy() ? '—' : 'Global';
        }

        return Tenant::query()->whereKey($tenantId)->value('name') ?? '—';
    }
}
