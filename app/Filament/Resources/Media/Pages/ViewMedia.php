<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use App\Models\MediaItem;
use App\Traits\Filament\HasBackButtonAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

/**
 * @property MediaItem $record
 */
class ViewMedia extends ViewRecord
{
    use HasBackButtonAction;

    protected static string $resource = MediaResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            EditAction::make()->icon(Heroicon::Pencil),
        ];
    }

    protected function resolveDynamicTitle(): string
    {
        $record = $this->getRecord();

        if (! $record instanceof MediaItem) {
            return 'Visualizar Mídia';
        }

        if ((bool) $record->video) {
            $title = $record->video()->value('title');

            return $title ?: 'Vídeo (URL)';
        }

        return $record->getFirstMedia('media')->name ?? 'Sem nome';
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
