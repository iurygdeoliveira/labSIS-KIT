<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use App\Traits\Filament\HasBackButtonAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

/**
 * @property \App\Models\MediaItem $record
 */
class DeleteMedia extends ViewRecord
{
    use HasBackButtonAction;

    protected static string $resource = MediaResource::class;

    #[\Override]
    public function getView(): string
    {
        return 'filament.resources.media.pages.delete-media';
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            Action::make('confirm_delete')
                ->label('Confirmar Exclusão')
                ->color('danger')
                ->icon('heroicon-s-trash')
                ->requiresConfirmation()
                ->modalHeading('Confirmar Exclusão Permanente')
                ->modalDescription('Esta ação não pode ser desfeita. O arquivo será excluído permanentemente.')
                ->modalSubmitActionLabel('Sim, Excluir')
                ->modalCancelActionLabel('Cancelar')
                ->action(function (): void {
                    $this->getRecord()->delete();

                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }

    protected function resolveDynamicTitle(): string
    {
        $record = $this->getRecord();

        if (! $record instanceof \App\Models\MediaItem) {
            return 'Excluir Mídia';
        }

        if ((bool) $record->video) {
            $title = $record->video()->value('title');

            return 'Excluir: '.($title ?: 'Vídeo (URL)');
        }

        $name = $record->getFirstMedia('media')->name ?? 'Sem nome';

        return "Excluir: {$name}";
    }

    #[\Override]
    public function getTitle(): string|Htmlable
    {
        return $this->resolveDynamicTitle();
    }

    #[\Override]
    public function getHeading(): string|Htmlable
    {
        return $this->resolveDynamicTitle();
    }
}
