<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $uuid = config('filament-tenant-members.key_type', 'id') === 'uuid';

        Schema::create('organization_invites', function (Blueprint $table) use ($uuid) {
            $uuid ? $table->uuid('id')->primary() : $table->id();
            $table->uuid('token')->unique();
            if ($uuid) {
                $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
                $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            } else {
                $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            }
            $table->string('email');
            $table->string('role')->default(config('filament-tenant-members.default_role', 'user'));
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_invites');
    }
};
