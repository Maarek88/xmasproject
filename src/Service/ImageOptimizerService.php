<?php

namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Symfony\Component\Filesystem\Filesystem;

use function exif_imagetype;

class ImageOptimizerService
{
    private const MAX_WIDTH = 1000;
    private const MAX_HEIGHT = 1000;
    private static $extensions = [
        IMAGETYPE_GIF => "gif",
        IMAGETYPE_JPEG => "jpg",
        IMAGETYPE_PNG => "png",
        IMAGETYPE_SWF => "swf",
        IMAGETYPE_PSD => "psd",
        IMAGETYPE_BMP => "bmp",
        IMAGETYPE_TIFF_II => "tiff",
        IMAGETYPE_TIFF_MM => "tiff",
        IMAGETYPE_JPC => "jpc",
        IMAGETYPE_JP2 => "jp2",
        IMAGETYPE_JPX => "jpx",
        IMAGETYPE_JB2 => "jb2",
        IMAGETYPE_SWC => "swc",
        IMAGETYPE_IFF => "iff",
        IMAGETYPE_WBMP => "wbmp",
        IMAGETYPE_XBM => "xbm",
        IMAGETYPE_ICO => "ico"
    ];

    private $imagine;
    private $fileSystem;

    public function __construct(Filesystem $fileSystem)
    {
        $this->imagine    = new Imagine();
        $this->fileSystem = $fileSystem;
    }

    public function resize(string $filename): void
    {
        $imageExtension = $this->getImageExtension($filename);

        if ($imageExtension === null) {
            return;
        }

        $tmpFilename = $filename.'.'.$imageExtension;

        list($imageWidth, $imageHeight) = getimagesize($filename);

        if ($imageWidth <= self::MAX_WIDTH && $imageHeight <= self::MAX_HEIGHT) {
            return;
        }

        $photo = $this->imagine->open($filename);
        $photo->resize(new Box(self::MAX_WIDTH, self::MAX_HEIGHT))->save($tmpFilename);

        $this->fileSystem->rename($tmpFilename, $filename, true);
    }

    public function getImageExtension(string $filename)
    {
        return self::$extensions[exif_imagetype($filename)] ?? null;
    }
}