<?php

namespace App\Filament\Pages;

use App\Models\OrganizationInvite;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class AcceptInvite extends Page
{
    const ERROR_INVALID = 'invalid';

    const ERROR_EMAIL_MISMATCH = 'email_mismatch';

    public ?string $token = null;

    public ?OrganizationInvite $invite = null;

    public ?string $error = null;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $layout = 'filament-panels::components.layout.simple';

    protected string $view = 'pages.accept-invite';

    public static function getRoutePath(Panel $panel): string
    {
        return '/accept-invite/{token}';
    }

    public static function getRelativeRouteName(Panel $panel): string
    {
        return 'accept-invite';
    }

    public function mount(string $token): void
    {
        $this->token = $token;

        if (! Filament::auth()->check()) {
            session()->put('pending_invite_token', $token);

            $this->redirect(Filament::getLoginUrl(), navigate: false);

            return;
        }

        $this->invite = OrganizationInvite::byToken($token)
            ->with(['organization', 'user'])
            ->pending()
            ->first();

        if (! $this->invite) {
            $this->error = self::ERROR_INVALID;

            return;
        }

        if (! $this->invite->matchesUser(Filament::auth()->user())) {
            $this->error = self::ERROR_EMAIL_MISMATCH;
        }
    }

    public function getTitle(): string|Htmlable
    {
        return __('organization.accept_invite.title');
    }

    public function getHeading(): string|Htmlable
    {
        return match ($this->error) {
            self::ERROR_INVALID => __('organization.accept_invite.invalid_heading'),
            self::ERROR_EMAIL_MISMATCH => __('organization.accept_invite.mismatch_heading'),
            default => __('organization.accept_invite.invited_heading'),
        };
    }

    public function getSubheading(): string|Htmlable|null
    {
        return match ($this->error) {
            self::ERROR_INVALID => __('organization.accept_invite.invalid_subheading'),
            self::ERROR_EMAIL_MISMATCH => __('organization.accept_invite.mismatch_subheading'),
            default => null,
        };
    }

    public function getViewData(): array
    {
        return [
            'invite' => $this->invite,
            'error' => $this->error,
        ];
    }

    public function acceptAction(): Action
    {
        return Action::make('accept')
            ->label(__('organization.actions.accept.label'))
            ->icon(Heroicon::OutlinedCheckCircle)
            ->color('primary')
            ->action(function (): void {
                if (! $this->canRespondToInvite()) {
                    $this->notifyCannotRespond();

                    return;
                }

                $this->invite->accept(Filament::auth()->user());

                Notification::make()
                    ->title(__('organization.notifications.invitation_accepted_title'))
                    ->body(__('organization.notifications.invitation_accepted_body', ['organization' => $this->invite->organization->name]))
                    ->success()
                    ->send();

                $this->redirect(
                    Filament::getUrl($this->invite->organization),
                    navigate: false,
                );
            });
    }

    public function declineAction(): Action
    {
        return Action::make('decline')
            ->label(__('organization.actions.decline.label'))
            ->icon(Heroicon::OutlinedXCircle)
            ->color('gray')
            ->outlined()
            ->requiresConfirmation()
            ->modalHeading(__('organization.actions.decline.modal_heading'))
            ->modalDescription(__('organization.actions.decline.modal_description'))
            ->action(function (): void {
                if (! $this->canRespondToInvite()) {
                    $this->notifyCannotRespond();

                    return;
                }

                $organizationName = $this->invite->organization->name;
                $this->invite->delete();

                Notification::make()
                    ->title(__('organization.notifications.invitation_declined_title'))
                    ->body(__('organization.notifications.invitation_declined_body', ['organization' => $organizationName]))
                    ->send();

                $this->redirect(Filament::getUrl(), navigate: false);
            });
    }

    protected function canRespondToInvite(): bool
    {
        return $this->invite !== null && $this->error === null;
    }

    protected function notifyCannotRespond(): void
    {
        Notification::make()
            ->title(__('organization.notifications.invitation_unavailable_title'))
            ->body($this->getSubheading())
            ->danger()
            ->send();
    }
}
