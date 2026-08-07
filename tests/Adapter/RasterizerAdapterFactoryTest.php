<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests\Adapter;

use Atelier\Rasterizer\Adapter\ResvgRasterizer;
use Atelier\Rasterizer\Adapter\ResvgRasterizerFactory;
use Atelier\Rasterizer\Adapter\RsvgConvertRasterizer;
use Atelier\Rasterizer\Adapter\RsvgConvertRasterizerFactory;
use Atelier\Rasterizer\Process\BinaryResolver;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\ExecutableFinder;

#[CoversClass(ResvgRasterizerFactory::class)]
#[CoversClass(RsvgConvertRasterizerFactory::class)]
final class RasterizerAdapterFactoryTest extends TestCase
{
    public function testResvgFactorySupportsResolvableBinary(): void
    {
        $factory = new ResvgRasterizerFactory(binaryResolver: new BinaryResolver(new FixedExecutableFinder('/usr/bin/resvg')));

        self::assertTrue($factory->supports());
        self::assertSame('resvg', $factory->name());
        self::assertInstanceOf(ResvgRasterizer::class, $factory->create());
    }

    public function testResvgFactoryDoesNotSupportMissingBinary(): void
    {
        $factory = new ResvgRasterizerFactory(binaryResolver: new BinaryResolver(new FixedExecutableFinder(null)));

        self::assertFalse($factory->supports());
    }

    public function testRsvgConvertFactorySupportsResolvableBinary(): void
    {
        $factory = new RsvgConvertRasterizerFactory(binaryResolver: new BinaryResolver(new FixedExecutableFinder('/usr/bin/rsvg-convert')));

        self::assertTrue($factory->supports());
        self::assertSame('rsvg-convert', $factory->name());
        self::assertInstanceOf(RsvgConvertRasterizer::class, $factory->create());
    }

    public function testRsvgConvertFactoryDoesNotSupportMissingBinary(): void
    {
        $factory = new RsvgConvertRasterizerFactory(binaryResolver: new BinaryResolver(new FixedExecutableFinder(null)));

        self::assertFalse($factory->supports());
    }
}

final class FixedExecutableFinder extends ExecutableFinder
{
    public function __construct(
        private readonly ?string $path,
    ) {
    }

    /**
     * @param list<string> $extraDirs
     */
    public function find(string $name, ?string $default = null, array $extraDirs = []): ?string
    {
        return $this->path ?? $default;
    }
}
