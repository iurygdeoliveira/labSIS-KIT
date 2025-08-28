<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Media;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar 2 imagens de exemplo (com manipulações)
        Media::factory(2)
            ->image()
            ->create();

        // Criar 2 documentos de exemplo
        Media::factory(2)
            ->document()
            ->create();

        // Criar 2 vídeos de exemplo
        Media::factory(2)
            ->video()
            ->create();

        // Criar 2 áudios de exemplo
        Media::factory(2)
            ->state(['mime_type' => 'audio/mpeg', 'collection_name' => 'audios'])
            ->create();
    }
}
