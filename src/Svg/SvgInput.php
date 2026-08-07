<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Svg;

use Atelier\Rasterizer\Exception\InvalidSvgInputException;

/**
 * Validated SVG markup, the single input form every rasterizer consumes.
 *
 * Construct it from markup (`fromString`), a file (`fromFile`), or a stream
 * (`fromStream`). A bare string or `\Stringable` passed to a rasterizer is
 * always treated as markup, so there is no ambiguity between markup and a file
 * path: reading a file or stream is an explicit, named operation.
 */
final readonly class SvgInput
{
    private function __construct(
        public string $markup,
    ) {
    }

    /**
     * Wraps SVG markup. Accepts any `\Stringable`, including an `Atelier\Svg\Svg`
     * document.
     *
     * @throws InvalidSvgInputException when the markup is empty or is not SVG
     */
    public static function fromString(\Stringable|string $svg): self
    {
        $markup = trim((string) $svg);

        if ('' === $markup) {
            throw InvalidSvgInputException::empty();
        }

        if (!str_contains($markup, '<svg')) {
            throw InvalidSvgInputException::notSvg();
        }

        return new self($markup);
    }

    /**
     * Reads SVG markup from a file path or `\SplFileInfo`.
     *
     * @throws InvalidSvgInputException when the file is unreadable or not SVG
     */
    public static function fromFile(string|\SplFileInfo $file): self
    {
        $path = $file instanceof \SplFileInfo ? $file->getPathname() : $file;
        $contents = @file_get_contents($path);

        if (false === $contents) {
            throw InvalidSvgInputException::fileNotReadable($path);
        }

        return self::fromString($contents);
    }

    /**
     * Reads SVG markup from an open stream resource.
     *
     * @param resource $stream
     *
     * @throws InvalidSvgInputException when the stream cannot be read or is not SVG
     */
    public static function fromStream($stream): self
    {
        if (!\is_resource($stream)) {
            throw InvalidSvgInputException::streamNotReadable();
        }

        return self::fromString((string) stream_get_contents($stream));
    }
}
