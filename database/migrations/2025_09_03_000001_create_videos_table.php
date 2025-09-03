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
            $table->text('description')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();

            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->decimal('aspect_ratio', 5, 2)->nullable();
            $table->decimal('frame_rate', 5, 2)->nullable();
            $table->unsignedInteger('bitrate_kbps')->nullable();

            $table->boolean('has_audio')->nullable();
            $table->string('audio_codec')->nullable();
            $table->unsignedSmallInteger('audio_channels')->nullable();
            $table->unsignedInteger('audio_sample_rate_hz')->nullable();

            $table->string('channel_id')->nullable();
            $table->string('channel_name')->nullable();
            $table->dateTime('published_at')->nullable();

            $table->unsignedBigInteger('view_count')->nullable();
            $table->unsignedBigInteger('like_count')->nullable();
            $table->unsignedBigInteger('comment_count')->nullable();
            $table->boolean('is_live')->default(false);
            $table->string('privacy_status')->nullable();
            $table->string('license')->nullable();

            $table->json('tags')->nullable();
            $table->json('categories')->nullable();
            $table->json('region_allowed')->nullable();
            $table->json('region_blocked')->nullable();
            $table->json('raw_payload')->nullable();

            $table->timestamps();

            $table->unique(['provider', 'provider_video_id']);
            $table->index('url');
        });

        Schema::table('media_items', function (Blueprint $table) {
            $table->boolean('video')->default(false)->after('id');
        });
    }
};
