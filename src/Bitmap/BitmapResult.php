<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Bitmap;

use Atelier\Rasterizer\Exception\RasterizationFailedException;

/**
 * The bitmap produced by a rasterizer, holding the encoded bytes in memory
 * alongside their format and dimensions.
 */
final readonly class BitmapResult
{
    public function __construct(
        public string $contents,
        public BitmapFormat $format,
        public ?int $width = null,
        public ?int $height = null,
        public ?string $mimeType = null,
    ) {
    }

    /**
     * Writes the bitmap bytes to a file.
     *
     * @throws RasterizationFailedException when the target directory is missing, is not writable, or the write fails
     */
    public function save(string $path): void
    {
        if (false === @file_put_contents($path, $this->contents)) {
            throw RasterizationFailedException::cannotWrite($path);
        }
    }
}
