<?php

namespace App\Filament\Resources\Authentication\Pages;

use App\Filament\Resources\Authentication\AuthenticationLogResource;
use Filament\Resources\Pages\ListRecords;

class ListAuthenticationLogs extends ListRecords
{
    protected static string $resource = AuthenticationLogResource::class;
}
