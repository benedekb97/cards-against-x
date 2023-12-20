<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class DeckImportFileUploaderService implements DeckImportFileUploaderServiceInterface
{
    public function __construct(
        private Filesystem            $filesystem,
        private ParameterBagInterface $parameterBag
    ) {}

    public function upload(UploadedFile $file): string
    {
        $fileName = $this->generateFileName();

        $extension = $file->guessExtension();

        if (
            (null === $extension ||
            'txt' === $extension) &&
            array_key_exists($file->getClientMimeType(), self::CLIENT_MIME_TYPE_MAP)
        ) {
            $extension = self::CLIENT_MIME_TYPE_MAP[$file->getClientMimeType()];
        }

        if (!$this->filesystem->exists(
            $dir = Path::makeAbsolute(self::UPLOAD_DIRECTORY, $this->parameterBag->get('kernel.project_dir'))
        )) {
            $this->filesystem->mkdir($dir);
        }

        $this->filesystem->dumpFile(
            $filePath = $this->getFullPath($fileName, $extension),
            $file->getContent()
        );

        return $filePath;
    }

    private function getFullPath(string $fileName, string $extension): string
    {
        return Path::makeAbsolute(
            sprintf(
                '%s/%s.%s',
                self::UPLOAD_DIRECTORY,
                $fileName,
                $extension
            ),
            $this->parameterBag->get('kernel.project_dir')
        );
    }

    private function generateFileName(): string
    {
        return sprintf(
            '%s_%d',
            (new \DateTime())->format('YmdHis'),
            uniqid()
        );
    }
}