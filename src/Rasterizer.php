<?php

declare(strict_types=1);

namespace Atelier\Rasterizer;

use Atelier\Rasterizer\Adapter\ResvgRasterizer;
use Atelier\Rasterizer\Adapter\RsvgConvertRasterizer;
use Psr\Log\LoggerInterface;

/**
 * Thin entry point for the default rasterizer selection.
 */
final class Rasterizer
{
    private function __construct()
    {
    }

    /**
     * Creates the first supported adapter in the package's default order.
     */
    public static function create(?LoggerInterface $logger = null): RasterizerInterface
    {
        return (new RasterizerFactory(logger: $logger))->create();
    }

    /**
     * Creates a resvg adapter without auto-detection.
     */
    public static function resvg(?LoggerInterface $logger = null): RasterizerInterface
    {
        return new ResvgRasterizer(logger: $logger);
    }

    /**
     * Creates an rsvg-convert adapter without auto-detection.
     */
    public static function rsvgConvert(?LoggerInterface $logger = null): RasterizerInterface
    {
        return new RsvgConvertRasterizer(logger: $logger);
    }
}
