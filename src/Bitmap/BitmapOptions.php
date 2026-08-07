<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Bitmap;

/**
 * Immutable description of the requested bitmap output: format, dimensions,
 * aspect-ratio behavior, scale, background, and process timeout. Every field
 * has a default.
 */
final readonly class BitmapOptions
{
    public function __construct(
        public BitmapFormat $format = BitmapFormat::Png,
        public ?int $width = null,
        public ?int $height = null,
        public bool $keepAspectRatio = true,
        public float $scale = 1.0,
        public ?string $background = null,
        public float $timeout = 30.0,
    ) {
    }
}
