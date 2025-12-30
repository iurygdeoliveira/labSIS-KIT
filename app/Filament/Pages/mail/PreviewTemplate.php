<?php

declare(strict_types=1);

namespace App\Filament\Pages\Mail;

use Filament\Pages\Page;
use Illuminate\Mail\Markdown;

class PreviewTemplate extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.mail.preview-template';

    protected static ?string $title = 'Preview do Template';

    protected static bool $shouldRegisterNavigation = false;

    public string $previewHtml = '';

    public function mount(): void
    {
        $markdown = app(Markdown::class);

        $content = <<<'MARKDOWN'
# Olá!

Esta é uma visualização de exemplo do seu template de email.

Aqui você pode verificar se as cores, fontes e estrutura estão de acordo com o desejado.

@component('mail::button', ['url' => '#'])
Botão de Ação
@endcomponent

Atenciosamente,
Equipe LabSIS
MARKDOWN;

        $this->previewHtml = $markdown->render('mail::message', [
            'slot' => $content,
            'subcopy' => 'Se você tiver problemas com o botão acima, copie e cole a URL no seu navegador.',
        ])->toHtml();
    }

    use \App\Traits\Filament\HasBackButtonAction;

    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction()
                ->url(Templates::getUrl()),
        ];
    }
}
