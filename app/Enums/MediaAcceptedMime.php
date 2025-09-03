<?php

declare(strict_types=1);

namespace App\Enums;

enum MediaAcceptedMime: string
{
    case ANY_IMAGE = 'image/*';
    case ANY_AUDIO = 'audio/*';
    case AUDIO_MPEG = 'audio/mpeg';
    case AUDIO_MP3 = 'audio/mp3';
    case AUDIO_WAV = 'audio/wav';
    case AUDIO_OGG = 'audio/ogg';
    case AUDIO_M4A = 'audio/m4a';
    case AUDIO_AAC = 'audio/aac';
    case APP_PDF = 'application/pdf';
    case APP_MSWORD = 'application/msword';
    case APP_DOCX = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    case APP_XLS = 'application/vnd.ms-excel';
    case APP_XLSX = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    case TEXT_PLAIN = 'text/plain';

    public static function defaults(): array
    {
        return [
            self::ANY_IMAGE->value,
            self::ANY_AUDIO->value,
            self::AUDIO_MPEG->value,
            self::AUDIO_MP3->value,
            self::AUDIO_WAV->value,
            self::AUDIO_OGG->value,
            self::AUDIO_M4A->value,
            self::AUDIO_AAC->value,
            self::APP_PDF->value,
            self::APP_MSWORD->value,
            self::APP_DOCX->value,
            self::APP_XLS->value,
            self::APP_XLSX->value,
            self::TEXT_PLAIN->value,
        ];
    }
}
