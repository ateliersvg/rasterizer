<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests\Double;

use Atelier\Rasterizer\Adapter\AbstractProcessRasterizer;
use Atelier\Rasterizer\Bitmap\BitmapFormat;
use Atelier\Rasterizer\Bitmap\BitmapOptions;

/**
 * Test double whose command never writes the output file, so the shared
 * workflow can be exercised without depending on a real rasterizer binary.
 */
final class StubProcessRasterizer extends AbstractProcessRasterizer
{
    protected function binaryName(): string
    {
        return 'php-stub-rasterizer';
    }

    protected function supportedFormats(): array
    {
        return [BitmapFormat::Png];
    }

    protected function buildCommand(string $binary, string $inputPath, string $outputPath, BitmapOptions $options): array
    {
        // Exits successfully without producing $outputPath.
        return [$binary, '-r', 'exit(0);'];
    }
}
