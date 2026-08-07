<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests\Bitmap;

use Atelier\Rasterizer\Bitmap\BitmapFormat;
use Atelier\Rasterizer\Bitmap\BitmapResult;
use Atelier\Rasterizer\Exception\RasterizationFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BitmapResult::class)]
final class BitmapResultTest extends TestCase
{
    public function testExposesMetadata(): void
    {
        $result = new BitmapResult('binary', BitmapFormat::Png, 120, 60, 'image/png');

        self::assertSame('binary', $result->contents);
        self::assertSame(BitmapFormat::Png, $result->format);
        self::assertSame(120, $result->width);
        self::assertSame(60, $result->height);
        self::assertSame('image/png', $result->mimeType);
    }

    public function testSaveWritesContentsToDisk(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'bitmap-result-').'.png';
        $result = new BitmapResult('pixels', BitmapFormat::Png);

        try {
            $result->save($path);

            self::assertSame('pixels', file_get_contents($path));
        } finally {
            @unlink($path);
        }
    }

    public function testSaveThrowsWhenDirectoryIsNotWritable(): void
    {
        $result = new BitmapResult('pixels', BitmapFormat::Png);

        $this->expectException(RasterizationFailedException::class);

        $result->save('/atelier-rasterizer-missing-dir/card.png');
    }
}
