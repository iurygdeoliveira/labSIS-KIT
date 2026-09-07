<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Media>
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

        $mimeType = fake()->randomElement($mimeTypes);
        $isImage = str_starts_with((string) $mimeType, 'image/');

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
        $fileName = fake()->slug().'.'.$extension;

        return [
            'uuid' => Str::uuid(),
            'collection_name' => fake()->randomElement(['images', 'documents', 'videos', 'avatars', 'gallery']),
            'name' => fake()->sentence(3),
            'file_name' => $fileName,
            'mime_type' => $mimeType,
            'disk' => fake()->randomElement(['public', 'local', 's3']),
            'conversions_disk' => fake()->optional()->randomElement(['public', 'local', 's3']),
            'size' => fake()->numberBetween(1024, 50 * 1024 * 1024), // 1KB a 50MB
            'manipulations' => $isImage ? [
                'thumb' => ['width' => 150, 'height' => 150, 'crop' => 'center'],
                'medium' => ['width' => 800, 'height' => 600, 'crop' => 'center'],
                'large' => ['width' => 1920, 'height' => 1080, 'crop' => 'center'],
            ] : [],
            'custom_properties' => [
                'alt_text' => fake()->sentence(),
                'description' => fake()->paragraph(),
                'tags' => fake()->words(3),
                'author' => fake()->name(),
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
            'order_column' => fake()->numberBetween(1, 100),
            'model_type' => fake()->randomElement([User::class, 'App\Models\Post', 'App\Models\Product']),
            'model_id' => fake()->numberBetween(1, 100),
        ];
    }

    /**
     * Indica que é uma imagem
     */
    public function image(): static
    {
        return $this->state(fn (array $attributes): array => [
            'mime_type' => fake()->randomElement(['image/jpeg', 'image/png', 'image/gif', 'image/webp']),
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
        return $this->state(fn (array $attributes): array => [
            'mime_type' => fake()->randomElement(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']),
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
        return $this->state(fn (array $attributes): array => [
            'mime_type' => fake()->randomElement(['video/mp4', 'video/avi']),
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
        return $this->state(fn (array $attributes): array => [
            'mime_type' => fake()->randomElement(['image/jpeg', 'image/png']),
            'collection_name' => 'avatars',
            'size' => fake()->numberBetween(1024, 5 * 1024 * 1024), // 1KB a 5MB
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
        return $this->state(fn (array $attributes): array => [
            'size' => fake()->numberBetween(1024, 100 * 1024), // 1KB a 100KB
        ]);
    }

    /**
     * Indica que é um arquivo grande (para testes de performance)
     */
    public function large(): static
    {
        return $this->state(fn (array $attributes): array => [
            'size' => fake()->numberBetween(10 * 1024 * 1024, 100 * 1024 * 1024), // 10MB a 100MB
        ]);
    }
}
