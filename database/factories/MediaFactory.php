<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'video/mp4',
            'video/avi',
            'audio/mpeg',
        ];

        $mimeType = $this->faker->randomElement($mimeTypes);
        $isImage = str_starts_with($mimeType, 'image/');
        $isVideo = str_starts_with($mimeType, 'video/');
        $isPdf = $mimeType === 'application/pdf';
        $isDocument = str_starts_with($mimeType, 'application/');

        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'video/mp4' => 'mp4',
            'video/avi' => 'avi',
            'audio/mpeg' => 'mp3',
        ];

        $extension = $extensions[$mimeType] ?? 'bin';
        $fileName = $this->faker->slug().'.'.$extension;

        return [
            'uuid' => Str::uuid(),
            'collection_name' => $this->faker->randomElement(['images', 'documents', 'videos', 'avatars', 'gallery']),
            'name' => $this->faker->sentence(3),
            'file_name' => $fileName,
            'mime_type' => $mimeType,
            'disk' => $this->faker->randomElement(['public', 'local', 's3']),
            'conversions_disk' => $this->faker->optional()->randomElement(['public', 'local', 's3']),
            'size' => $this->faker->numberBetween(1024, 50 * 1024 * 1024), // 1KB a 50MB
            'manipulations' => $isImage ? [
                'thumb' => ['width' => 150, 'height' => 150, 'crop' => 'center'],
                'medium' => ['width' => 800, 'height' => 600, 'crop' => 'center'],
                'large' => ['width' => 1920, 'height' => 1080, 'crop' => 'center'],
            ] : [],
            'custom_properties' => [
                'alt_text' => $this->faker->sentence(),
                'description' => $this->faker->paragraph(),
                'tags' => $this->faker->words(3),
                'author' => $this->faker->name(),
            ],
            'generated_conversions' => $isImage ? [
                'thumb' => true,
                'medium' => true,
                'large' => true,
            ] : [],
            'responsive_images' => $isImage ? [
                'thumb' => true,
                'medium' => true,
                'large' => true,
            ] : [],
            'order_column' => $this->faker->numberBetween(1, 100),
            'model_type' => $this->faker->randomElement(['App\Models\User', 'App\Models\Post', 'App\Models\Product']),
            'model_id' => $this->faker->numberBetween(1, 100),
        ];
    }

    /**
     * Indica que é uma imagem
     */
    public function image(): static
    {
        return $this->state(fn (array $attributes) => [
            'mime_type' => $this->faker->randomElement(['image/jpeg', 'image/png', 'image/gif', 'image/webp']),
            'collection_name' => 'images',
            'manipulations' => [
                'thumb' => ['width' => 150, 'height' => 150, 'crop' => 'center'],
                'medium' => ['width' => 800, 'height' => 600, 'crop' => 'center'],
                'large' => ['width' => 1920, 'height' => 1080, 'crop' => 'center'],
            ],
            'generated_conversions' => [
                'thumb' => true,
                'medium' => true,
                'large' => true,
            ],
            'responsive_images' => [
                'thumb' => true,
                'medium' => true,
                'large' => true,
            ],
        ]);
    }

    /**
     * Indica que é um documento
     */
    public function document(): static
    {
        return $this->state(fn (array $attributes) => [
            'mime_type' => $this->faker->randomElement(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']),
            'collection_name' => 'documents',
            'manipulations' => [],
            'generated_conversions' => [],
            'responsive_images' => [],
        ]);
    }

    /**
     * Indica que é um vídeo
     */
    public function video(): static
    {
        return $this->state(fn (array $attributes) => [
            'mime_type' => $this->faker->randomElement(['video/mp4', 'video/avi']),
            'collection_name' => 'videos',
            'manipulations' => [],
            'generated_conversions' => [],
            'responsive_images' => [],
        ]);
    }

    /**
     * Indica que é um avatar
     */
    public function avatar(): static
    {
        return $this->state(fn (array $attributes) => [
            'mime_type' => $this->faker->randomElement(['image/jpeg', 'image/png']),
            'collection_name' => 'avatars',
            'size' => $this->faker->numberBetween(1024, 5 * 1024 * 1024), // 1KB a 5MB
            'manipulations' => [
                'thumb' => ['width' => 100, 'height' => 100, 'crop' => 'center'],
                'medium' => ['width' => 300, 'height' => 300, 'crop' => 'center'],
            ],
            'generated_conversions' => [
                'thumb' => true,
                'medium' => true,
            ],
            'responsive_images' => [
                'thumb' => true,
                'medium' => true,
            ],
        ]);
    }

    /**
     * Indica que é um arquivo pequeno (para testes de performance)
     */
    public function small(): static
    {
        return $this->state(fn (array $attributes) => [
            'size' => $this->faker->numberBetween(1024, 100 * 1024), // 1KB a 100KB
        ]);
    }

    /**
     * Indica que é um arquivo grande (para testes de performance)
     */
    public function large(): static
    {
        return $this->state(fn (array $attributes) => [
            'size' => $this->faker->numberBetween(10 * 1024 * 1024, 100 * 1024 * 1024), // 10MB a 100MB
        ]);
    }
}
