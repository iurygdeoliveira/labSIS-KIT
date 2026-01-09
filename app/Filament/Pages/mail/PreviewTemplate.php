<?php

declare(strict_types=1);

namespace App\Filament\Pages\Mail;

use Filament\Pages\Page;
use Livewire\Attributes\Url;

class PreviewTemplate extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.mail.preview-template';

    protected static bool $shouldRegisterNavigation = false;

    #[\Override]
    public function getTitle(): string
    {
        return match ($this->type) {
            'password-reset' => 'Preview: Redefinição de Senha',
            default => 'Preview do Template',
        };
    }

    #[Url]
    public ?string $type = 'default';

    public string $previewHtml = '';

    public function mount(): void
    {
        if ($this->type === 'password-reset') {
            $this->renderPasswordReset();
        }
    }

    protected function renderPasswordReset(): void
    {
        $user = \Filament\Facades\Filament::auth()->user();

        $this->previewHtml = view('vendor.mail.html.password-reset', [
            'user' => $user,
            'token' => 'sample-token',
            'url' => '#',
        ])->render();
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
