<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Aws\S3\S3Client;
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

        $key = config('filesystems.disks.s3.key');
        $secret = config('filesystems.disks.s3.secret');
        $bucket = config('filesystems.disks.s3.bucket');

        if (! $key || ! $secret) {
            $this->warn('S3 credentials not found. Skipping S3 initialization.');

            return;
        }

        try {
            // 1. Criar cliente S3 para gerenciar bucket
            $client = new S3Client([
                'version' => 'latest',
                'region' => config('filesystems.disks.s3.region', 'us-east-1'),
                'endpoint' => config('filesystems.disks.s3.endpoint'),
                'use_path_style_endpoint' => config('filesystems.disks.s3.use_path_style_endpoint', false),
                'credentials' => [
                    'key' => $key,
                    'secret' => $secret,
                ],
            ]);

            // 2. Verificar e criar bucket se não existir
            if (! $client->doesBucketExist($bucket)) {
                $this->comment("Bucket '{$bucket}' not found. Creating...");
                $client->createBucket(['Bucket' => $bucket]);
                $this->info("✅ Bucket '{$bucket}' created successfully.");
            } else {
                $this->comment("Bucket '{$bucket}' already exists.");
            }

            // 3. Criar diretórios padrão
            $directories = ['audios', 'images', 'documents', 'avatar'];

            foreach ($directories as $directory) {
                // Check if directory exists implicitly (checking for .keep file or just creating it)
                // makeDirectory in S3 driver creates a 0-byte object with trailing slash or mimics it
                Storage::disk('s3')->makeDirectory($directory);

                // Ensure visibility is correct by putting a file
                Storage::disk('s3')->put(
                    "{$directory}/.keep",
                    '',
                    ['visibility' => 'private']
                );

                $this->comment("Checked directory: {$directory}");
            }

            $this->info('✨ Storage initialization completed successfully.');

        } catch (\Exception $e) {
            $this->error("❌ Failed to initialize storage: {$e->getMessage()}");
        }
    }
}
