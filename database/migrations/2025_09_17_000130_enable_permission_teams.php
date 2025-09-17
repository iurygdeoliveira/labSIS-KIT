<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableNames = config('permission.table_names');
        $teamKey = config('permission.column_names.team_foreign_key', 'team_id');

        // roles: adicionar coluna de team e ajustar unique
        if (! Schema::hasColumn($tableNames['roles'], $teamKey)) {
            Schema::table($tableNames['roles'], function (Blueprint $table) use ($teamKey) {
                $table->unsignedBigInteger($teamKey)->nullable()->after('id');
                $table->index($teamKey, 'roles_'.$teamKey.'_index');
            });

            // Remover unique antigo (name, guard_name)
            try {
                DB::statement('ALTER TABLE '.$tableNames['roles'].' DROP CONSTRAINT IF EXISTS roles_name_guard_name_unique');
            } catch (Throwable $e) {
                // ignora se já não existir
            }

            // Adicionar unique novo (team_id, name, guard_name)
            Schema::table($tableNames['roles'], function (Blueprint $table) use ($teamKey) {
                $table->unique([$teamKey, 'name', 'guard_name'], 'roles_team_name_guard_unique');
            });
        }

        // model_has_roles: adicionar team e redefinir PK
        if (! Schema::hasColumn($tableNames['model_has_roles'], $teamKey)) {
            // Adicionar como NOT NULL com default 0 para não violar dados existentes (global)
            Schema::table($tableNames['model_has_roles'], function (Blueprint $table) use ($teamKey) {
                $table->unsignedBigInteger($teamKey)->default(0)->after('model_id');
                $table->index($teamKey, 'model_has_roles_'.$teamKey.'_index');
            });

            // Garantir que linhas antigas tenham valor 0
            DB::table($tableNames['model_has_roles'])->whereNull($teamKey)->update([$teamKey => 0]);

            // Ajustar a primary key para incluir o team
            try {
                DB::statement('ALTER TABLE '.$tableNames['model_has_roles'].' DROP CONSTRAINT IF EXISTS model_has_roles_role_model_type_primary');
            } catch (Throwable $e) {
            }
            try {
                DB::statement('ALTER TABLE '.$tableNames['model_has_roles'].' DROP CONSTRAINT IF EXISTS '.$tableNames['model_has_roles'].'_pkey');
            } catch (Throwable $e) {
            }
            DB::statement('ALTER TABLE '.$tableNames['model_has_roles'].' ADD PRIMARY KEY ("'.$teamKey.'", "role_id", "model_id", "model_type")');
        }

        // model_has_permissions: adicionar team e redefinir PK
        if (! Schema::hasColumn($tableNames['model_has_permissions'], $teamKey)) {
            Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) use ($teamKey) {
                $table->unsignedBigInteger($teamKey)->default(0)->after('model_id');
                $table->index($teamKey, 'model_has_permissions_'.$teamKey.'_index');
            });

            DB::table($tableNames['model_has_permissions'])->whereNull($teamKey)->update([$teamKey => 0]);

            try {
                DB::statement('ALTER TABLE '.$tableNames['model_has_permissions'].' DROP CONSTRAINT IF EXISTS model_has_permissions_permission_model_type_primary');
            } catch (Throwable $e) {
            }
            try {
                DB::statement('ALTER TABLE '.$tableNames['model_has_permissions'].' DROP CONSTRAINT IF EXISTS '.$tableNames['model_has_permissions'].'_pkey');
            } catch (Throwable $e) {
            }
            DB::statement('ALTER TABLE '.$tableNames['model_has_permissions'].' ADD PRIMARY KEY ("'.$teamKey.'", "permission_id", "model_id", "model_type")');
        }
    }
};
