<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
class FreshAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:fresh-all {--seed : Seed the database after migrating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop PostgreSQL and MongoDB databases, then migrate PostgreSQL';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->confirm('⚠️  This will DROP all data in PostgreSQL AND MongoDB. Continue?')) {
            $this->info('Operation cancelled.');

            return self::FAILURE;
        }

        $this->newLine();

        // 1. Limpar MongoDB
        $this->info('🗑️  Dropping MongoDB database...');

        try {
            DB::connection('mongodb')->getDatabase()->drop();
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
