<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Exception;

/**
 * Thrown when the rasterizer input is empty or is not SVG markup.
 */
final class InvalidSvgInputException extends \InvalidArgumentException implements ExceptionInterface
{
    public static function empty(): self
    {
        return new self('SVG input is empty.');
    }

    public static function notSvg(): self
    {
        return new self('SVG input does not contain an <svg> root element.');
    }

    public static function fileNotReadable(string $path): self
    {
        return new self(\sprintf('SVG input file "%s" does not exist or is not readable.', $path));
    }

    public static function streamNotReadable(): self
    {
        return new self('SVG input stream is not a readable resource.');
    }
}
