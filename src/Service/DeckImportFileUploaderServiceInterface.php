<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface DeckImportFileUploaderServiceInterface
{
    public const UPLOAD_DIRECTORY = 'var/storage/deck-import/';

    public const CLIENT_MIME_TYPE_MAP = [
        'application/x-yaml' => 'yaml',
        'application/yaml' => 'yaml',
        'application/json' => 'json',
    ];

    public function upload(UploadedFile $file): string;
}