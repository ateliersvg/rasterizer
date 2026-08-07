<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests\Bitmap;

use Atelier\Rasterizer\Bitmap\BitmapFormat;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BitmapFormat::class)]
final class BitmapFormatTest extends TestCase
{
    public function testMimeTypeMapsEachCase(): void
    {
        self::assertSame('image/png', BitmapFormat::Png->mimeType());
        self::assertSame('image/jpeg', BitmapFormat::Jpeg->mimeType());
        self::assertSame('image/webp', BitmapFormat::Webp->mimeType());
    }

    public function testExtensionMatchesValue(): void
    {
        self::assertSame('png', BitmapFormat::Png->extension());
        self::assertSame('jpg', BitmapFormat::Jpeg->extension());
        self::assertSame('webp', BitmapFormat::Webp->extension());
    }
}
