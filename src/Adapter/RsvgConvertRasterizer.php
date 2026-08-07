<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Adapter;

use Atelier\Rasterizer\Bitmap\BitmapFormat;
use Atelier\Rasterizer\Bitmap\BitmapOptions;

/**
 * Rasterizes through librsvg's rsvg-convert binary.
 */
final class RsvgConvertRasterizer extends AbstractProcessRasterizer
{
    protected function binaryName(): string
    {
        return 'rsvg-convert';
    }

    protected function supportedFormats(): array
    {
        return [BitmapFormat::Png];
    }

    protected function buildCommand(string $binary, string $inputPath, string $outputPath, BitmapOptions $options): array
    {
        $command = [
            $binary,
            '--format', $options->format->value,
            '--output', $outputPath,
        ];

        if (null !== $options->width) {
            $command[] = '--width';
            $command[] = (string) $options->width;
        }

        if (null !== $options->height) {
            $command[] = '--height';
            $command[] = (string) $options->height;
        }

        if ($options->keepAspectRatio && null !== $options->width && null !== $options->height) {
            $command[] = '--keep-aspect-ratio';
        }

        if (1.0 !== $options->scale) {
            $command[] = '--zoom';
            $command[] = (string) $options->scale;
        }

        if (null !== $options->background) {
            $command[] = '--background-color';
            $command[] = $options->background;
        }

        $command[] = $inputPath;

        return $command;
    }
}
