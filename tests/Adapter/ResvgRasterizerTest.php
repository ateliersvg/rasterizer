<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests\Adapter;

use Atelier\Rasterizer\Adapter\ResvgRasterizer;
use Atelier\Rasterizer\Bitmap\BitmapFormat;
use Atelier\Rasterizer\Bitmap\BitmapOptions;
use Atelier\Rasterizer\Exception\UnsupportedFormatException;
use Atelier\Rasterizer\Svg\SvgInput;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\ExecutableFinder;

#[CoversClass(ResvgRasterizer::class)]
#[Group('system')]
final class ResvgRasterizerTest extends TestCase
{
    private const string PNG_SIGNATURE = "\x89PNG\r\n\x1a\n";

    protected function setUp(): void
    {
        if (null === (new ExecutableFinder())->find('resvg')) {
            self::markTestSkipped('resvg is not installed.');
        }
    }

    public function testRasterizesFromSvgInputFile(): void
    {
        $result = (new ResvgRasterizer())->rasterize(SvgInput::fromFile(__DIR__.'/../Fixtures/simple.svg'));

        self::assertStringStartsWith(self::PNG_SIGNATURE, $result->contents);
    }

    public function testProducesPngBitmap(): void
    {
        $result = (new ResvgRasterizer())->rasterize($this->fixture());

        self::assertSame(BitmapFormat::Png, $result->format);
        self::assertSame('image/png', $result->mimeType);
        self::assertStringStartsWith(self::PNG_SIGNATURE, $result->contents);
    }

    public function testWidthOptionIsApplied(): void
    {
        $result = (new ResvgRasterizer())->rasterize($this->fixture(), new BitmapOptions(width: 200));

        self::assertSame(200, $this->pngWidth($result->contents));
        self::assertSame(200, $result->width);
    }

    public function testHeightOptionIsApplied(): void
    {
        $result = (new ResvgRasterizer())->rasterize($this->fixture(), new BitmapOptions(height: 150));

        self::assertSame(150, $this->pngHeight($result->contents));
        self::assertSame(150, $result->height);
    }

    public function testKeepsAspectRatioWhenWidthAndHeightAreSet(): void
    {
        $result = (new ResvgRasterizer())->rasterize($this->wideFixture(), new BitmapOptions(width: 200, height: 200));

        self::assertSame(200, $this->pngWidth($result->contents));
        self::assertSame(100, $this->pngHeight($result->contents));
        self::assertSame(200, $result->width);
        self::assertSame(100, $result->height);
    }

    public function testScaleOptionZoomsOutput(): void
    {
        $result = (new ResvgRasterizer())->rasterize($this->fixture(), new BitmapOptions(scale: 2.0));

        self::assertSame(200, $this->pngWidth($result->contents));
    }

    public function testBackgroundOptionProducesBitmap(): void
    {
        $result = (new ResvgRasterizer())->rasterize($this->fixture(), new BitmapOptions(background: '#ffffff'));

        self::assertStringStartsWith(self::PNG_SIGNATURE, $result->contents);
    }

    public function testUnsupportedFormatThrows(): void
    {
        $this->expectException(UnsupportedFormatException::class);

        (new ResvgRasterizer())->rasterize($this->fixture(), new BitmapOptions(format: BitmapFormat::Jpeg));
    }

    private function fixture(): string
    {
        return (string) file_get_contents(__DIR__.'/../Fixtures/simple.svg');
    }

    private function wideFixture(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="50" viewBox="0 0 100 50"><rect width="100" height="50" fill="red"/></svg>';
    }

    private function pngWidth(string $png): int
    {
        $unpacked = unpack('Nwidth', substr($png, 16, 4));
        self::assertIsArray($unpacked);

        return $unpacked['width'];
    }

    private function pngHeight(string $png): int
    {
        $unpacked = unpack('Nheight', substr($png, 20, 4));
        self::assertIsArray($unpacked);

        return $unpacked['height'];
    }
}
