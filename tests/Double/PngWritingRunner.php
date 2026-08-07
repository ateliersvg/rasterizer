<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests\Double;

use Atelier\Rasterizer\Process\ProcessRunnerInterface;

final class PngWritingRunner implements ProcessRunnerInterface
{
    public function run(array $command, float $timeout = 30.0): void
    {
        $outputPath = $this->outputPath($command);

        if (null !== $outputPath) {
            file_put_contents($outputPath, self::png1x1());
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

    private static function png1x1(): string
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=', true) ?: '';
    }
}
