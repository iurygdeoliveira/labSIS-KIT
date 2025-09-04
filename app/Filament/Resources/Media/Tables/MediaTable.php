<?php

namespace App\Filament\Resources\Media\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
                            return $record->video()->value('title') ?? 'Vídeo (URL)';
                        }

                        return $record->getFirstMedia('media')?->name ?? '—';
                    })
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                TextColumn::make('file_type')
                    ->label('Tipo')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Imagem' => 'primary',
                        'Vídeo' => 'warning',
                        'Documento' => 'success',
                        'Áudio' => 'danger'
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Imagem' => 'heroicon-c-photo',
                        'Vídeo' => 'heroicon-c-video-camera',
                        'Documento' => 'heroicon-c-document',
                        'Áudio' => 'heroicon-c-musical-note',
                    }),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
