<?php

declare(strict_types=1);

namespace Atelier\Rasterizer;

use Atelier\Rasterizer\Bitmap\BitmapOptions;
use Atelier\Rasterizer\Bitmap\BitmapResult;
use Atelier\Rasterizer\Svg\SvgInput;

/**
 * Contract for rasterizing an SVG document into a bitmap.
 *
 * Implemented by adapters, each bound to one external rendering binary.
 */
interface RasterizerInterface
{
    /**
     * Rasterizes SVG into a bitmap.
     *
     * A bare string or `\Stringable` (such as an `Atelier\Svg\Svg` document) is
     * treated as markup. Use `SvgInput::fromFile()` or `SvgInput::fromStream()`
     * for other sources.
     *
     * @throws Exception\ExceptionInterface when input is invalid, the format is unsupported, or rasterization fails
     */
    public function rasterize(SvgInput|\Stringable|string $svg, BitmapOptions $options = new BitmapOptions()): BitmapResult;
}
