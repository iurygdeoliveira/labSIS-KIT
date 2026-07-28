<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Changelog;
use App\Support\Changelog\KeepAChangelogParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SyncChangelogFromGithub extends Command
{
    protected $signature = 'changelog:sync-github 
                            {--url= : URL raw do arquivo CHANGELOG.md no GitHub} 
                            {--file= : Caminho de um arquivo CHANGELOG.md local} 
                            {--fresh : Limpar tabela de changelogs antes de sincronizar}';

    protected $description = 'Sincroniza os registros de changelog a partir do GitHub (ou arquivo local)';

    public function handle(KeepAChangelogParser $parser): int
    {
        $url = $this->option('url') ?: env('CHANGELOG_GITHUB_URL');
        $file = $this->option('file');

        $markdown = null;

        if ($file) {
            if (! file_exists($file)) {
                $this->error("Arquivo local não encontrado: {$file}");

                return self::FAILURE;
            }
            $markdown = file_get_contents($file);
            $this->info("Lendo changelog do arquivo: {$file}");
        } elseif ($url) {
            $this->info("Baixando changelog do GitHub: {$url}");
            $response = Http::timeout(10)->get($url);
            if (! $response->successful()) {
                $this->error('Falha ao fazer download do GitHub. Código HTTP: '.$response->status());

                return self::FAILURE;
            }
            $markdown = $response->body();
        } elseif (file_exists(base_path('CHANGELOG.md'))) {
            $markdown = file_get_contents(base_path('CHANGELOG.md'));
            $this->info('Lendo changelog do arquivo padrão: '.base_path('CHANGELOG.md'));
        } else {
            $this->error('Nenhuma fonte especificada (--url, --file) e CHANGELOG.md local não encontrado.');

            return self::FAILURE;
        }

        if (empty($markdown)) {
            $this->warn('Conteúdo do changelog está vazio.');

            return self::SUCCESS;
        }

        $entries = $parser->parse($markdown);
        $this->info('Foram processados '.count($entries).' itens no changelog.');

        if ($this->option('fresh')) {
            $this->warn('Limpeza da tabela (--fresh) ativada. Excluindo registros existentes...');
            Changelog::truncate();
        }

        $count = 0;
        foreach ($entries as $item) {
            Changelog::updateOrCreate(
                [
                    'version' => $item['version'],
                    'type' => $item['type']->value,
                    'description' => $item['description'],
                ],
                [
                    'released_at' => $item['released_at'],
                    'is_released' => $item['is_released'],
                    'sort' => $item['sort'],
                ]
            );
            $count++;
        }

        Cache::forget('changelog:latest_date');

        $this->info("Sincronização concluída com sucesso! {$count} registros atualizados/criados.");

        return self::SUCCESS;
    }
}
