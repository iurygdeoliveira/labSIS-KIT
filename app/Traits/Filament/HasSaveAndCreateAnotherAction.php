<?php

namespace App\Traits\Filament;

use Filament\Actions\Action;

trait HasSaveAndCreateAnotherAction
{
    protected function getCreateAnotherFormAction(): Action
    {
        return Action::make('createAnother')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.create_another.label'))
            ->action('createAnother')
            ->keyBindings(['mod+shift+s'])
            ->icon('heroicon-s-plus')
            ->color('save-another');
    }
}
