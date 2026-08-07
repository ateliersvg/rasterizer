<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Adapter;

use Atelier\Rasterizer\Bitmap\BitmapFormat;
use Atelier\Rasterizer\Bitmap\BitmapOptions;

/**
 * Rasterizes through the resvg binary. resvg emits PNG only.
 */
final class ResvgRasterizer extends AbstractProcessRasterizer
{
    protected function binaryName(): string
    {
        return 'resvg';
    }

    protected function supportedFormats(): array
    {
        return [BitmapFormat::Png];
    }

    protected function buildCommand(string $binary, string $inputPath, string $outputPath, BitmapOptions $options): array
    {
        $command = [$binary];

        if (null !== $options->width) {
            $command[] = '--width';
            $command[] = (string) $options->width;
        }

        if (null !== $options->height) {
            $command[] = '--height';
            $command[] = (string) $options->height;
        }

        if (1.0 !== $options->scale) {
            $command[] = '--zoom';
            $command[] = (string) $options->scale;
        }

        if (null !== $options->background) {
            $command[] = '--background';
            $command[] = $options->background;
        }

        $command[] = $inputPath;
        $command[] = $outputPath;

        return $command;
    }
}
