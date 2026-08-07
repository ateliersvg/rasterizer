<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests\Adapter;

use Atelier\Rasterizer\Adapter\AbstractProcessRasterizer;
use Atelier\Rasterizer\Adapter\ResvgRasterizer;
use Atelier\Rasterizer\Adapter\RsvgConvertRasterizer;
use Atelier\Rasterizer\Bitmap\BitmapOptions;
use Atelier\Rasterizer\Tests\Double\PngWritingRunner;
use Atelier\Rasterizer\Tests\Double\TraceableRunner;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractProcessRasterizer::class)]
#[CoversClass(ResvgRasterizer::class)]
#[CoversClass(RsvgConvertRasterizer::class)]
final class RasterizerCommandTest extends TestCase
{
    private const string SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"></svg>';

    public function testResvgMapsBitmapOptionsToCommand(): void
    {
        $runner = new TraceableRunner(new PngWritingRunner());

        (new ResvgRasterizer(processRunner: $runner, binaryPath: '/bin/resvg'))->rasterize(
            self::SVG,
            new BitmapOptions(width: 1200, height: 630, scale: 2.0, background: '#ffffff', timeout: 5.0),
        );

        $command = $this->singleCommand($runner);

        self::assertSame('/bin/resvg', $command[0]);
        self::assertContains('--width', $command);
        self::assertContains('1200', $command);
        self::assertContains('--height', $command);
        self::assertContains('630', $command);
        self::assertContains('--zoom', $command);
        self::assertContains('2', $command);
        self::assertContains('--background', $command);
        self::assertContains('#ffffff', $command);
        self::assertNotContains('--keep-aspect-ratio', $command);
        self::assertSame([5.0], $runner->timeouts);
    }

    public function testRsvgConvertKeepsAspectRatioByDefaultWhenWidthAndHeightAreSet(): void
    {
        $runner = new TraceableRunner(new PngWritingRunner());

        (new RsvgConvertRasterizer(processRunner: $runner, binaryPath: '/bin/rsvg-convert'))->rasterize(
            self::SVG,
            new BitmapOptions(width: 1200, height: 630),
        );

        $command = $this->singleCommand($runner);

        self::assertSame('/bin/rsvg-convert', $command[0]);
        self::assertContains('--format', $command);
        self::assertContains('png', $command);
        self::assertContains('--output', $command);
        self::assertContains('--width', $command);
        self::assertContains('1200', $command);
        self::assertContains('--height', $command);
        self::assertContains('630', $command);
        self::assertContains('--keep-aspect-ratio', $command);
    }

    public function testRsvgConvertCanDisableAspectRatioPreservation(): void
    {
        $runner = new TraceableRunner(new PngWritingRunner());

        (new RsvgConvertRasterizer(processRunner: $runner, binaryPath: '/bin/rsvg-convert'))->rasterize(
            self::SVG,
            new BitmapOptions(width: 1200, height: 630, keepAspectRatio: false),
        );

        self::assertNotContains('--keep-aspect-ratio', $this->singleCommand($runner));
    }

    public function testRsvgConvertDoesNotAddKeepAspectRatioForSingleDimension(): void
    {
        $runner = new TraceableRunner(new PngWritingRunner());

        (new RsvgConvertRasterizer(processRunner: $runner, binaryPath: '/bin/rsvg-convert'))->rasterize(
            self::SVG,
            new BitmapOptions(width: 1200),
        );

        self::assertNotContains('--keep-aspect-ratio', $this->singleCommand($runner));
    }

    /**
     * @return list<string>
     */
    private function singleCommand(TraceableRunner $runner): array
    {
        self::assertCount(1, $runner->commands);

        return $runner->commands[0];
    }
}
