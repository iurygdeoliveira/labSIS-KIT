<?php

declare(strict_types=1);

namespace App\Traits\Filament;

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

        match ($type) {
            'primary' => $notification->success()
                ->icon('heroicon-s-check-circle')
                ->iconColor('primary')
                ->persistent()
                ->duration(500)
                ->color('primary'),

            'danger' => $notification->danger()
                ->icon('heroicon-c-no-symbol')
                ->iconColor('danger')
                ->color('danger'),

            'warning' => $notification->warning()
                ->icon('heroicon-s-exclamation-triangle')
                ->iconColor('warning')
                ->color('warning'),

            default => $notification->info(),
        };

        if ($persistent) {
            $notification->persistent();
        } else {
            $notification->seconds($seconds);
        }

        return $notification;
    }
}
