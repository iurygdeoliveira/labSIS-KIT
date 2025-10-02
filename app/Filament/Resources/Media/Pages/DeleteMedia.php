<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use App\Traits\Filament\HasBackButtonAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class DeleteMedia extends ViewRecord
{
    use HasBackButtonAction;

    protected static string $resource = MediaResource::class;

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
                ->action(function () {
                    $this->getRecord()->delete();

                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }

    protected function resolveDynamicTitle(): string
    {
        $record = $this->getRecord();

        if (! $record) {
            return 'Excluir Mídia';
        }

        if ((bool) $record->video) {
            $title = $record->video()->value('title');

            return 'Excluir: '.($title ?: 'Vídeo (URL)');
        }

        $name = $record->getFirstMedia('media')?->name ?? 'Sem nome';

        return "Excluir: {$name}";
    }

    public function getTitle(): string|Htmlable
    {
        return $this->resolveDynamicTitle();
    }

    public function getHeading(): string|Htmlable
    {
        return $this->resolveDynamicTitle();
    }
}
