<?php

namespace App\Traits\Filament;

use Override;

trait HasStandardCreateHeaderActions
{
    use HasBackButtonAction;
    use HasSaveAndCreateAnotherAction;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            $this->getCreateFormAction()
                ->label('Salvar')
                ->icon('heroicon-s-check')
                ->formId('form'),
            $this->getCreateAnotherFormAction()
                ->formId('form'),
            $this->getCancelFormAction()
                ->color('danger')
                ->icon('heroicon-s-x-mark'),
        ];
    }
}
