<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use App\Filament\Resources\Media\Widgets\MediaStats;
use Aws\S3\S3Client;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMedia extends ListRecords
{
    protected static string $resource = MediaResource::class;

    public $defaultAction = 'onboarding';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MediaStats::class,
        ];
    }

    protected function isBucketAccessible(): bool
    {
        $bucket = (string) config('filesystems.disks.s3.bucket', 'labsis');

        try {
            $client = new S3Client([
                'version' => 'latest',
                'region' => (string) config('filesystems.disks.s3.region', 'us-east-1'),
                'endpoint' => (string) config('filesystems.disks.s3.endpoint', (string) config('filesystems.disks.s3.url')),
                'use_path_style_endpoint' => (bool) config('filesystems.disks.s3.use_path_style_endpoint', true),
                'credentials' => [
                    'key' => (string) config('filesystems.disks.s3.key'),
                    'secret' => (string) config('filesystems.disks.s3.secret'),
                ],
            ]);

            $client->headBucket(['Bucket' => $bucket]);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    public function onboardingAction(): Action
    {
        return Action::make('warnBucketMissing')
            ->modal()
            ->modalHeading('Bucket LabSIS não encontrado')
            ->modalDescription('O bucket "labsis" não existe no MinIO ou não está acessível. É necessário criar o bucket "labsis" manualmente no MinIO para que a gestão de midias funcione corretamente.')
            ->modalIcon('heroicon-o-exclamation-triangle')
            ->modalIconColor('warning')
            ->modalSubmitAction(false)
            ->modalCancelAction(fn (Action $action) => $action
                ->label('Entendi')
                ->color('warning')
            )
            ->visible(fn (): bool => ! $this->isBucketAccessible());
    }
}
