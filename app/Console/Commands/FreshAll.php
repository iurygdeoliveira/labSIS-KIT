<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use MongoDB\Laravel\Connection;

/**
 * Comando personalizado para limpar PostgreSQL e MongoDB simultaneamente.
 *
 * Este comando executa:
 * 1. Drop do database MongoDB
 * 2. migrate:fresh no PostgreSQL
 * 3. Opcionalmente executa seeders
 *
 * Útil para resetar completamente o ambiente de desenvolvimento,
 * incluindo logs de autenticação armazenados no MongoDB.
 */
#[Description('Drop PostgreSQL and MongoDB databases, then migrate PostgreSQL')]
#[Signature('migrate:fresh-all {--seed : Seed the database after migrating}')]
class FreshAll extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // 1. Limpar MongoDB
        $this->info('🗑️  Dropping MongoDB database...');

        try {
            /** @var Connection $connection */
            $connection = DB::connection('mongodb');
            $connection->getDatabase()->drop();
            $this->info('✅ MongoDB cleared!');
        } catch (\Exception $e) {
            $this->error("❌ Failed to drop MongoDB: {$e->getMessage()}");

            return self::FAILURE;
        }

        $this->newLine();

        // 2. Executar migrate:fresh no PostgreSQL
        $this->info('🗑️  Running migrate:fresh on PostgreSQL...');

        $exitCode = $this->call('migrate:fresh', [
            '--force' => true,
            '--seed' => $this->option('seed'),
        ]);

        if ($exitCode !== self::SUCCESS) {
            $this->error('❌ migrate:fresh failed!');

            return $exitCode;
        }

        $this->newLine();
        $this->info('✨ Both databases cleared and PostgreSQL migrated successfully!');

        // Estatísticas finais
        $this->newLine();
        $this->table(
            ['Database', 'Status'],
            [
                ['PostgreSQL', '✅ Migrated'.($this->option('seed') ? ' & Seeded' : '')],
                ['MongoDB', '✅ Cleared'],
            ]
        );

        return self::SUCCESS;
    }
}
