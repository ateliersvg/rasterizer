<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Process;

use Atelier\Rasterizer\Exception\BinaryNotFoundException;
use Symfony\Component\Process\ExecutableFinder;

/**
 * Resolves a rasterizer binary to an absolute path, preferring an explicitly
 * configured path over a PATH lookup.
 */
final readonly class BinaryResolver
{
    public function __construct(
        private ExecutableFinder $finder = new ExecutableFinder(),
    ) {
    }

    /**
     * Resolves a binary to an absolute path, preferring an explicitly configured one.
     */
    public function resolve(string $name, ?string $configuredPath = null): string
    {
        if (null !== $configuredPath && '' !== $configuredPath) {
            return $configuredPath;
        }

        $path = $this->finder->find($name);

        if (null === $path) {
            throw BinaryNotFoundException::for($name);
        }

        return $path;
    }
}
