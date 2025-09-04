<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use App\Trait\Filament\HasBackButtonAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewMedia extends ViewRecord
{
    use HasBackButtonAction;

    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            EditAction::make(),
        ];
    }

    protected function resolveDynamicTitle(): string
    {
        $record = $this->getRecord();

        if ((bool) $record->video) {
            $title = $record->video()->value('title');

            return $title ?: 'Vídeo (URL)';
        }

        return $record->getFirstMedia('media')?->name ?? 'Sem nome';
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
