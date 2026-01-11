<?php

namespace App\Traits\Filament;

use Override;

trait HasStandardCreateFooterActions
{
    use HasBackButtonAction;
    use HasSaveAndCreateAnotherAction;

    #[Override]
    protected function getFormActions(): array
    {
        return [
            $this->getBackButtonAction(),
            $this->getCreateFormAction()
                ->label('Salvar')
                ->icon('heroicon-s-check'),
            $this->getCreateAnotherFormAction(),
            $this->getCancelFormAction()
                ->color('danger')
                ->icon('heroicon-s-x-mark'),
        ];
    }
}
