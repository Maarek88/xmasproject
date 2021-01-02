<?php

namespace App\Service;

use Exception;
use Symfony\Component\Filesystem\Filesystem;

class DownloadService
{
    private $fileSystem;

    public function __construct(Filesystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    public function downloadFromUrl(string $url, string $downloadFilePath): bool
    {
        try {
            $this->fileSystem->dumpFile($downloadFilePath, file_get_contents($url));
        } catch (Exception $e) {
        }

        return $this->fileSystem->exists($downloadFilePath);
    }
}
