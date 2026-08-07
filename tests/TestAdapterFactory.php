<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests;

use Atelier\Rasterizer\Adapter\RasterizerAdapterFactoryInterface;
use Atelier\Rasterizer\Bitmap\BitmapFormat;
use Atelier\Rasterizer\Bitmap\BitmapOptions;
use Atelier\Rasterizer\Bitmap\BitmapResult;
use Atelier\Rasterizer\RasterizerInterface;
use Atelier\Rasterizer\Svg\SvgInput;

final class TestAdapterFactory implements RasterizerAdapterFactoryInterface
{
    public int $supportsCalls = 0;

    public int $createCalls = 0;

    public readonly RasterizerInterface $rasterizer;

    public function __construct(
        private readonly string $name,
        public bool $supported,
        ?RasterizerInterface $rasterizer = null,
    ) {
        $this->rasterizer = $rasterizer ?? new TestRasterizer();
    }

    public function name(): string
    {
        return $this->name;
    }

    public function supports(): bool
    {
        ++$this->supportsCalls;

        return $this->supported;
    }

    public function create(): RasterizerInterface
    {
        ++$this->createCalls;

        return $this->rasterizer;
    }
}

final class TestRasterizer implements RasterizerInterface
{
    public function rasterize(SvgInput|\Stringable|string $svg, BitmapOptions $options = new BitmapOptions()): BitmapResult
    {
        return new BitmapResult(
            contents: 'png',
            format: BitmapFormat::Png,
            width: null,
            height: null,
            mimeType: 'image/png',
        );
    }
}
