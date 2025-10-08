<?php

namespace App\Services;

use App\Mail\EmailVerificationMail;
use App\Mail\NewUserNotificationMail;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function sendWelcomeEmail(User $user, ?string $password = null, ?string $tenantName = null): void
    {
        Mail::to($user->email)->send(new WelcomeEmail($user, $password, $tenantName));
    }

    public function sendEmailVerification(User $user): void
    {
        Mail::to($user->email)->send(new EmailVerificationMail($user));
    }

    public function sendNewUserNotification(User $admin, User $newUser): void
    {
        Mail::to($admin->email)->send(new NewUserNotificationMail($admin, $newUser));
    }

    public function sendPasswordReset(User $user, string $token): void
    {
        // Implementar se necessário
    }
}
