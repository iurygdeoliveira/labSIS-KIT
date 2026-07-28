<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('changelogs', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('project')->nullable()->index();
            $table->string('version')->index();
            $table->date('released_at')->nullable();
            $table->text('description');
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();

            $table->index(['project', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('changelogs');
    }
};
