<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetChangelogUnread extends Command
{
    protected $signature = 'changelog:reset-unread';

    protected $description = 'Reseta o status de leitura do changelog para todos os usuários, forçando a exibição da notificação';

    public function handle(): int
    {
        $adminUsers = User::role(\App\Enums\OrganizationRole::Admin->value)->get();

        if ($adminUsers->isEmpty()) {
            $this->warn('Nenhum usuário administrador encontrado.');
            return self::SUCCESS;
        }

        $adminIds = $adminUsers->pluck('id')->toArray();

        $userCount = User::whereIn('id', $adminIds)->update(['last_read_changelog_at' => null]);

        $updatedNotifs = DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->whereIn('notifiable_id', $adminIds)
            ->update(['read_at' => null]);

        \Illuminate\Support\Facades\Cache::forget('changelog:latest_date');

        if (\App\Models\Changelog::query()->count() === 0) {
            $this->call('changelog:sync-git');
        }

        $this->info("Central de Notificações resetada com sucesso para {$userCount} usuários administradores.");
        $this->info("Total de notificações marcadas como não lidas: {$updatedNotifs}.");
        $this->info("Na próxima vez que o admin acessar o sistema, a notificação de nova atualização surgirá na Central de Notificações!");

        return self::SUCCESS;
    }
}
