<?php

declare(strict_types=1);

namespace App\Support\NotificationCenter\Concerns;

use Filament\Actions\Exports\Models\Export;
use Filament\Notifications\Notification;

trait CategorizesExportNotifications
{
    public static function modifyCompletedNotification(Notification $notification, Export $export): Notification
    {
        return static::categorizeExportNotification($notification);
    }

    public static function categorizeExportNotification(Notification $notification): Notification
    {
        return $notification->category('exports');
    }
}
