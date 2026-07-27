<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:test-categorized-notification {--user=1 : ID do usuário receptor} {--category=system : Categoria (ex: system, security, backups, exports, imports)}')]
#[Description('Dispara uma notificação categorizada de teste no Notification Center nativo')]
class TestCategorizedNotificationCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userId = (int) $this->option('user');
        $category = (string) $this->option('category');

        $user = User::find($userId);

        if ($user === null) {
            $this->error("Usuário com ID [{$userId}] não encontrado!");

            return self::FAILURE;
        }

        Notification::make()
            ->title("Notificação de Teste [{$category}]")
            ->body("Disparada via linha de comando no painel para testar a aba '{$category}'.")
            ->success()
            ->category($category)
            ->sendToDatabase($user);

        $this->info("✅ Notificação enviada com sucesso para o usuário [{$user->name}] na categoria [{$category}]!");

        return self::SUCCESS;
    }
}
