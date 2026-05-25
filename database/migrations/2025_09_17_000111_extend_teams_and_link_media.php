<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Estender a tabela `teams` (criada pelo pacote laraveldaily/filateams)
        // com `is_active` (controle administrativo do labSIS-KIT).
        Schema::table('teams', function (Blueprint $table): void {
            $table->boolean('is_active')->default(true)->after('is_personal');
        });

        // Relação de domínio: media_items pertence a um team
        Schema::table('media_items', function (Blueprint $table): void {
            $table->foreignId('team_id')
                ->nullable()
                ->after('id')
                ->constrained('teams')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('media_items', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('team_id');
        });

        Schema::table('teams', function (Blueprint $table): void {
            $table->dropColumn('is_active');
        });
    }
};
