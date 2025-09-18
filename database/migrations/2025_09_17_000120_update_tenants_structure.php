<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('tenants', 'slug')) {
            Schema::table('tenants', function (Blueprint $table): void {
                $table->dropColumn('slug');
            });
        }

        if (Schema::hasColumn('tenant_user', 'is_owner')) {
            Schema::table('tenant_user', function (Blueprint $table): void {
                $table->dropColumn('is_owner');
            });
        }
    }
};
