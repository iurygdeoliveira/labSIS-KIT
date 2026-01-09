<?php

declare(strict_types=1);

namespace App\Support\MediaLibrary;

use DateTimeInterface;
use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class SignedUrlGenerator extends DefaultUrlGenerator
{
    #[\Override]
    public function getUrl(): string
    {
        $minutes = (int) config('media-library.temporary_url_default_lifetime', 5);

        return $this->getTemporaryUrl(now()->addMinutes($minutes));
    }

    #[\Override]
    public function getTemporaryUrl(DateTimeInterface $expiration, array $options = []): string
    {
        return parent::getTemporaryUrl($expiration, $options);
    }
}
