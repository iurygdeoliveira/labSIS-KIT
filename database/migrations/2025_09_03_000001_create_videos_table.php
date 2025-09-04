<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('media_item_id')
                ->constrained('media_items')
                ->cascadeOnDelete()
                ->unique();

            $table->string('provider')->nullable();
            $table->string('provider_video_id')->nullable();
            $table->string('url');
            $table->string('title')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();

            $table->timestamps();

            $table->unique(['provider', 'provider_video_id']);
            $table->index('url');
        });

        Schema::table('media_items', function (Blueprint $table) {
            $table->boolean('video')->default(false)->after('id');
        });
    }
};
