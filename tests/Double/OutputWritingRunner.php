<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests\Double;

use Atelier\Rasterizer\Process\ProcessRunnerInterface;

/**
 * Writes a fixed byte string to the command's output path, so the shared
 * workflow can be driven with output that is non-empty yet shorter than a
 * PNG header (to exercise the dimension fallback) without a real binary.
 */
final class OutputWritingRunner implements ProcessRunnerInterface
{
    public function __construct(
        private readonly string $contents,
    ) {
    }

    public function run(array $command, float $timeout = 30.0): void
    {
        $outputPath = $this->outputPath($command);

        if (null !== $outputPath) {
            file_put_contents($outputPath, $this->contents);
        }
    }

    /**
     * @param list<string> $command
     */
    private function outputPath(array $command): ?string
    {
        foreach ($command as $index => $argument) {
            if ('--output' === $argument) {
                return $command[$index + 1] ?? null;
            }
        }

        foreach (array_reverse($command) as $argument) {
            if (str_ends_with($argument, '.png')) {
                return $argument;
            }
        }

        return null;
    }
}
