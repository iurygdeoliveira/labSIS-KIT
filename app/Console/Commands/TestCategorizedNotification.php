<?php

namespace App\Console\Commands;

use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:test-categorized-notification')]
#[Description('Envia notificações de teste categorizadas para o primeiro usuário')]
class TestCategorizedNotification extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $user = User::first();

        if (! $user) {
            $this->error('Nenhum usuário encontrado no banco de dados. Por favor, rode os seeders primeiro.');

            return 1;
        }

        $this->info("Enviando notificações categorizadas para o usuário: {$user->email}");

        // Notificação de Backup
        Notification::make()
            ->title('Backup Realizado com Sucesso')
            ->body('O backup automático do banco de dados foi concluído com sucesso.')
            ->success()
            ->category('backups')
            ->sendToDatabase($user);

        // Notificação de Segurança
        Notification::make()
            ->title('Novo Dispositivo Detectado')
            ->body('Um novo login foi efetuado a partir do IP 192.168.1.100.')
            ->warning()
            ->category('security')
            ->sendToDatabase($user);

        // Notificação Geral (Uncategorized)
        Notification::make()
            ->title('Mensagem Geral do Sistema')
            ->body('Esta é uma notificação informativa padrão sem categoria definida.')
            ->info()
            ->sendToDatabase($user);

        $this->info('Notificações enviadas com sucesso!');

        return 0;
    }
}
