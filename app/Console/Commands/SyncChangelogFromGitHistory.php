<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Changelog;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

#[Description('Sincroniza os registros de changelog a partir do histórico de commits do Git')]
#[Signature('changelog:sync-git 
                            {--fresh : Limpar tabela de changelogs antes de sincronizar}')]
class SyncChangelogFromGitHistory extends Command
{
    public function handle(): int
    {
        if ($this->option('fresh')) {
            $this->warn('Limpeza da tabela (--fresh) ativada. Excluindo registros existentes...');
            Changelog::truncate();
        }

        $this->info('Buscando histórico de commits...');

        $output = shell_exec('git log --pretty=format:"%h|%s|%as"');

        if (empty($output)) {
            $this->warn('Nenhum commit encontrado ou não foi possível acessar o git.');

            return self::FAILURE;
        }

        $lines = explode("\n", trim($output));
        $count = 0;
        $sort = 0;

        foreach ($lines as $line) {
            $parts = explode('|', $line, 3);

            if (count($parts) < 3) {
                continue;
            }

            [$hash, $subject, $date] = $parts;

            Changelog::updateOrCreate(
                [
                    'version' => $hash,
                ],
                [
                    'description' => $subject,
                    'released_at' => $date,
                    'sort' => $sort++,
                ]
            );
            $count++;
        }

        Cache::forget('changelog:latest_date');

        $this->info("Sincronização concluída com sucesso! {$count} commits sincronizados no changelog.");

        return self::SUCCESS;
    }
}
