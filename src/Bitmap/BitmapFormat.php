<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Bitmap;

/**
 * A bitmap output format and its associated MIME type and file extension.
 */
enum BitmapFormat: string
{
    case Png = 'png';
    case Jpeg = 'jpg';
    case Webp = 'webp';

    /**
     * The IANA media type for this format.
     */
    public function mimeType(): string
    {
        return match ($this) {
            self::Png => 'image/png',
            self::Jpeg => 'image/jpeg',
            self::Webp => 'image/webp',
        };
    }

    /**
     * The file extension for this format, without a leading dot.
     */
    public function extension(): string
    {
        return $this->value;
    }
}
