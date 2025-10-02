<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Image;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AvatarService
{
    public function getAvatarUrl(User $user): ?string
    {
        $media = $user->getFirstMedia('avatar');

        if ($media) {
            return $this->getMediaUrl($media);
        }

        return $this->getLegacyAvatarUrl($user);
    }

    public function processAndSaveAvatar(User $user, string $path): bool
    {
        $disk = (string) config('filament-edit-profile.disk', 's3');
        $contents = Storage::disk($disk)->get($path);

        if ($contents === null || $contents === '') {
            return false;
        }

        $tmpPath = $this->createTemporaryFile($contents);

        if ($tmpPath === null) {
            return false;
        }

        try {
            $this->resizeAndOptimizeImage($tmpPath);
            $this->saveToMediaCollection($user, $tmpPath, basename($path));
            $this->cleanupTemporaryFile($tmpPath);
            $this->deleteOriginalFile($disk, $path);

            return true;
        } catch (\Throwable) {
            $this->cleanupTemporaryFile($tmpPath);

            return false;
        }
    }

    private function getMediaUrl(Media $media): ?string
    {
        try {
            return $media->getUrl();
        } catch (\Throwable) {
            return null;
        }
    }

    private function getLegacyAvatarUrl(User $user): ?string
    {
        $avatarColumn = config('filament-edit-profile.avatar_column', 'avatar_url');

        if (! $user->$avatarColumn) {
            return null;
        }

        try {
            return $this->getTemporaryUrl($user->$avatarColumn);
        } catch (\Throwable) {
            return $this->getPublicUrl($user->$avatarColumn);
        }
    }

    private function getTemporaryUrl(string $path): ?string
    {
        try {
            /** @var \Illuminate\Contracts\Filesystem\Filesystem&\Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk(config('filament-edit-profile.disk', 's3'));

            return $disk->temporaryUrl($path, now()->addMinutes(5));
        } catch (\Throwable) {
            return null;
        }
    }

    private function getPublicUrl(string $path): ?string
    {
        try {
            return Storage::url($path);
        } catch (\Throwable) {
            return null;
        }
    }

    private function createTemporaryFile(string $contents): ?string
    {
        $tmpPath = tempnam(sys_get_temp_dir(), 'avatar_');

        if ($tmpPath === false) {
            return null;
        }

        file_put_contents($tmpPath, $contents);

        return $tmpPath;
    }

    private function resizeAndOptimizeImage(string $path): void
    {
        Image::load($path)
            ->fit(Fit::Crop, 256, 256)
            ->optimize()
            ->save($path);
    }

    private function saveToMediaCollection(User $user, string $tmpPath, string $fileName): void
    {
        $user->clearMediaCollection('avatar');

        $user->addMedia($tmpPath)
            ->usingFileName($fileName)
            ->toMediaCollection('avatar');
    }

    private function cleanupTemporaryFile(string $path): void
    {
        @unlink($path);
    }

    private function deleteOriginalFile(string $disk, string $path): void
    {
        Storage::disk($disk)->delete($path);
    }
}
