<?php

declare(strict_types=1);

namespace App\Support\NotificationCenter\Concerns;

use Filament\Actions\Imports\Models\Import;
use Filament\Notifications\Notification;

trait CategorizesImportNotifications
{
    public static function modifyCompletedNotification(Notification $notification, Import $import): Notification
    {
        return static::categorizeImportNotification($notification);
    }

    public static function categorizeImportNotification(Notification $notification): Notification
    {
        return $notification->category('imports');
    }
}
