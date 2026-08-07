<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Exception;

/**
 * Thrown when a rasterizer fails to produce a bitmap: the process exited
 * unsuccessfully, timed out, produced no output, or the result could not be
 * written or its temporary file created.
 */
final class RasterizationFailedException extends \RuntimeException implements ExceptionInterface
{
    /**
     * The rasterizer process exited with a non-zero status.
     *
     * @param list<string> $command
     */
    public static function fromProcess(array $command, ?int $exitCode, string $errorOutput): self
    {
        $message = \sprintf(
            'Rasterization command "%s" failed with exit code %s.',
            implode(' ', $command),
            $exitCode ?? 'unknown',
        );

        if ('' !== trim($errorOutput)) {
            $message .= ' '.trim($errorOutput);
        }

        return new self($message);
    }

    /**
     * The rasterizer process ran longer than the allowed timeout.
     *
     * @param list<string> $command
     */
    public static function timedOut(array $command, float $timeout, ?\Throwable $previous = null): self
    {
        return new self(
            \sprintf('Rasterization command "%s" timed out after %.1f seconds.', implode(' ', $command), $timeout),
            0,
            $previous,
        );
    }

    /**
     * The rasterizer succeeded but wrote no bytes.
     */
    public static function emptyOutput(string $binary): self
    {
        return new self(\sprintf('"%s" produced no output.', $binary));
    }

    /**
     * A temporary input or output file could not be created.
     */
    public static function cannotCreateTemporaryFile(): self
    {
        return new self('Unable to create a temporary file for rasterization.');
    }

    /**
     * The bitmap could not be written to the target path.
     */
    public static function cannotWrite(string $path): self
    {
        return new self(\sprintf('Unable to write bitmap to "%s".', $path));
    }
}
