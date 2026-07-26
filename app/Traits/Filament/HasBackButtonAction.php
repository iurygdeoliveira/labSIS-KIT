<?php

declare(strict_types=1);

namespace App\Traits\Filament;

use Filament\Actions\Action;
use Filament\Resources\Resource;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

/**
 * @method static class-string<Resource> getResource()
 */
trait HasBackButtonAction
{
    /**
     * Retorna a URL de retorno para a listagem (index) do recurso.
     */
    protected function getBackUrl(): ?string
    {
        if (property_exists($this, 'backUrl') && is_string($this->backUrl)) {
            return $this->backUrl;
        }

        if (method_exists(static::class, 'getResource')) {
            /** @var class-string<resource> $resource */
            $resource = static::getResource();

            return $resource::getUrl('index');
        }

        return null;
    }

    /**
     * Sobrescreve o título da página para renderizar o botão voltar no lado esquerdo do título.
     */
    #[\Override]
    public function getHeading(): string|Htmlable
    {
        $title = parent::getHeading();
        $backUrl = $this->getBackUrl();

        if (! $backUrl) {
            return $title;
        }

        return new HtmlString(
            view('filament.components.back-button-heading', [
                'title' => $title,
                'url' => $backUrl,
            ])->render()
        );
    }

    /**
     * Mantido para compatibilidade. Retorna uma ação oculta para evitar
     * duplicidade no cabeçalho caso a ação seja invocada em getHeaderActions().
     */
    protected function getBackButtonAction(): Action
    {
        $action = Action::make('back')
            ->label('Voltar')
            ->color('secondary')
            ->icon('heroicon-s-arrow-left')
            ->hidden();

        $backUrl = $this->getBackUrl();

        if ($backUrl) {
            $action->url($backUrl);
        }

        return $action;
    }
}
