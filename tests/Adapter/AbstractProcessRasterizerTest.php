<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests\Adapter;

use Atelier\Rasterizer\Adapter\AbstractProcessRasterizer;
use Atelier\Rasterizer\Adapter\ResvgRasterizer;
use Atelier\Rasterizer\Bitmap\BitmapFormat;
use Atelier\Rasterizer\Bitmap\BitmapOptions;
use Atelier\Rasterizer\Exception\RasterizationFailedException;
use Atelier\Rasterizer\Exception\UnsupportedFormatException;
use Atelier\Rasterizer\Tests\Double\OutputWritingRunner;
use Atelier\Rasterizer\Tests\Double\PngWritingRunner;
use Atelier\Rasterizer\Tests\Double\StubProcessRasterizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractProcessRasterizer::class)]
final class AbstractProcessRasterizerTest extends TestCase
{
    private const string SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"></svg>';

    public function testRejectsUnsupportedFormatBeforeRunning(): void
    {
        $this->expectException(UnsupportedFormatException::class);

        $this->stub()->rasterize(self::SVG, new BitmapOptions(format: BitmapFormat::Jpeg));
    }

    public function testThrowsWhenBinaryProducesNoOutput(): void
    {
        $this->expectException(RasterizationFailedException::class);
        $this->expectExceptionMessage('produced no output');

        $this->stub()->rasterize(self::SVG);
    }

    public function testThrowsWhenTemporaryFileCannotBeCreated(): void
    {
        $this->expectException(RasterizationFailedException::class);

        $this->stub('/atelier-rasterizer/nonexistent-directory')->rasterize(self::SVG);
    }

    public function testCleansUpTemporaryFilesOnFailure(): void
    {
        $directory = sys_get_temp_dir().'/atelier-rasterizer-cleanup-'.bin2hex(random_bytes(4));
        mkdir($directory);

        try {
            try {
                $this->stub($directory)->rasterize(self::SVG);
                self::fail('Expected rasterization to fail.');
            } catch (RasterizationFailedException) {
                // Expected: empty output.
            }

            self::assertSame([], glob($directory.'/atelier-rasterizer-*') ?: []);
        } finally {
            rmdir($directory);
        }
    }

    public function testFallsBackToOptionDimensionsWhenOutputIsTooShortForPngHeader(): void
    {
        $rasterizer = new ResvgRasterizer(
            processRunner: new OutputWritingRunner('short'),
            binaryPath: '/bin/resvg',
        );

        $result = $rasterizer->rasterize(self::SVG, new BitmapOptions(width: 320, height: 240));

        self::assertSame(320, $result->width);
        self::assertSame(240, $result->height);
    }

    public function testReadsDimensionsFromValidPngHeader(): void
    {
        $rasterizer = new ResvgRasterizer(
            processRunner: new PngWritingRunner(),
            binaryPath: '/bin/resvg',
        );

        $result = $rasterizer->rasterize(self::SVG, new BitmapOptions(width: 320, height: 240));

        self::assertSame(1, $result->width);
        self::assertSame(1, $result->height);
    }

    private function stub(?string $temporaryDirectory = null): StubProcessRasterizer
    {
        // binaryPath bypasses discovery so the configured-path branch is used.
        return new StubProcessRasterizer(binaryPath: \PHP_BINARY, temporaryDirectory: $temporaryDirectory);
    }
}
