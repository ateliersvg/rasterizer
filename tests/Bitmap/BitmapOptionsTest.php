<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests\Bitmap;

use Atelier\Rasterizer\Bitmap\BitmapFormat;
use Atelier\Rasterizer\Bitmap\BitmapOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BitmapOptions::class)]
final class BitmapOptionsTest extends TestCase
{
    public function testDefaults(): void
    {
        $options = new BitmapOptions();

        self::assertSame(BitmapFormat::Png, $options->format);
        self::assertNull($options->width);
        self::assertNull($options->height);
        self::assertTrue($options->keepAspectRatio);
        self::assertSame(1.0, $options->scale);
        self::assertNull($options->background);
        self::assertSame(30.0, $options->timeout);
    }

    public function testNamedArguments(): void
    {
        $options = new BitmapOptions(
            format: BitmapFormat::Png,
            width: 1200,
            height: 630,
            keepAspectRatio: false,
            scale: 2.0,
            background: '#ffffff',
            timeout: 5.0,
        );

        self::assertSame(1200, $options->width);
        self::assertSame(630, $options->height);
        self::assertFalse($options->keepAspectRatio);
        self::assertSame(2.0, $options->scale);
        self::assertSame('#ffffff', $options->background);
        self::assertSame(5.0, $options->timeout);
    }
}
