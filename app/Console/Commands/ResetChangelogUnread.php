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
        $userCount = User::query()->update(['last_read_changelog_at' => null]);

        $deletedNotifs = DB::table('notifications')->delete();

        \Illuminate\Support\Facades\Cache::forget('changelog:latest_date');

        if (\App\Models\Changelog::query()->count() === 0 && file_exists(base_path('CHANGELOG.md'))) {
            $this->call('changelog:sync-github');
        }

        $this->info("Central de Notificações resetada com sucesso para {$userCount} usuários.");
        $this->info("Todas as notificações antigas foram removidas: {$deletedNotifs}.");
        $this->info("Na próxima vez que qualquer usuário (incluindo admin) acessar o sistema, a notificação de nova atualização surgirá na Central de Notificações!");

        return self::SUCCESS;
    }
}
