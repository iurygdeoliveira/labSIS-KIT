<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class InitStorageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize S3 storage directories and configurations';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Initializing storage configuration...');

        if (! config('filesystems.disks.s3.key') || ! config('filesystems.disks.s3.secret')) {
            $this->warn('S3 credentials not found. Skipping S3 initialization.');

            return;
        }

        $directories = ['audios', 'images', 'documents', 'avatar'];

        try {
            foreach ($directories as $directory) {
                $this->comment("Ensuring directory exists: {$directory}");

                Storage::disk('s3')->makeDirectory($directory);

                Storage::disk('s3')->put(
                    "{$directory}/.keep",
                    '',
                    [
                        'visibility' => 'private',
                    ]
                );
            }

            $this->info('Storage initialization completed successfully.');
        } catch (\Exception $e) {
            $this->error("Failed to initialize storage: {$e->getMessage()}");
            // Non-zero exit code to indicate failure if needed,
            // but for this specific "soft" requirement, we might want to just log usage.
            // However, seeing the error is useful manually.
        }
    }
}
