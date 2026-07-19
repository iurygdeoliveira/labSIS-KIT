<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Estender a tabela `organizations` (criada pelo pacote filament-tenant-members)
        // com `is_active` (controle administrativo do labSIS-KIT).
        Schema::table('organizations', function (Blueprint $table): void {
            $table->boolean('is_active')->default(true)->after('slug');
        });

        // Relação de domínio: media_items pertence a uma organização
        Schema::table('media_items', function (Blueprint $table): void {
            $table->foreignId('organization_id')
                ->nullable()
                ->after('id')
                ->constrained('organizations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('media_items', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('organization_id');
        });

        Schema::table('organizations', function (Blueprint $table): void {
            $table->dropColumn('is_active');
        });
    }
};
