<?php

declare(strict_types=1);

namespace App\Trait\Filament;

use Filament\Notifications\Notification;

trait NotificationsTrait
{
    public function notifySuccess(string $title, ?string $body = null, int $seconds = 8, bool $persistent = false): void
    {
        $this->buildNotification('primary', $title, $body, $seconds, $persistent)->send();
    }

    public function notifyDanger(string $title, ?string $body = null, int $seconds = 8, bool $persistent = false): void
    {
        $this->buildNotification('danger', $title, $body, $seconds, $persistent)->send();
    }

    public function notifyWarning(string $title, ?string $body = null, int $seconds = 8, bool $persistent = false): void
    {
        $this->buildNotification('warning', $title, $body, $seconds, $persistent)->send();
    }

    protected function buildNotification(string $type, string $title, ?string $body = null, int $seconds = 8, bool $persistent = false): Notification
    {
        $notification = Notification::make()->title($title);

        if ($body !== null) {
            $notification->body($body);
        }

        switch ($type) {
            case 'primary':
                $notification->success()
                    ->icon('heroicon-s-check-circle')
                    ->iconColor('primary')
                    ->color('primary');

                break;

            case 'danger':
                $notification->danger()
                    ->icon('heroicon-c-no-symbol')
                    ->iconColor('danger')
                    ->color('danger');

                break;

            case 'warning':
                $notification->warning()
                    ->icon('heroicon-s-exclamation-triangle')
                    ->iconColor('warning')
                    ->color('warning');

                break;
        }

        if ($persistent) {
            $notification->persistent();
        } else {
            $notification->seconds($seconds);
        }

        return $notification;
    }
}
