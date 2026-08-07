<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Exception;

/**
 * Thrown when a rasterizer binary cannot be located in PATH or at the
 * configured path.
 */
final class BinaryNotFoundException extends \RuntimeException implements ExceptionInterface
{
    public static function for(string $name): self
    {
        return new self(\sprintf('Rasterizer binary "%s" was not found in PATH. Install it or pass an explicit path.', $name));
    }
}
