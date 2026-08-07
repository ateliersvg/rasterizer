<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Exception;

use Atelier\Rasterizer\Bitmap\BitmapFormat;

/**
 * Thrown when an adapter is asked for a format its binary cannot produce.
 */
final class UnsupportedFormatException extends \InvalidArgumentException implements ExceptionInterface
{
    /**
     * @param list<BitmapFormat> $supported
     */
    public static function for(BitmapFormat $requested, string $binary, array $supported): self
    {
        $names = implode(', ', array_map(static fn (BitmapFormat $format): string => $format->value, $supported));

        return new self(\sprintf('"%s" cannot produce the "%s" format. Supported: %s.', $binary, $requested->value, $names));
    }
}
