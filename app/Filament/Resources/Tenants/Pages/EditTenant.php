<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $users = (array) ($data['usersIds'] ?? []);
        unset($data['usersIds']);

        $record->update($data);
        $record->users()->sync($users);

        return $record;
    }
}
