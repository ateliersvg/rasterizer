<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests;

use Atelier\Rasterizer\Adapter\ResvgRasterizer;
use Atelier\Rasterizer\Adapter\RsvgConvertRasterizer;
use Atelier\Rasterizer\Rasterizer;
use Atelier\Rasterizer\RasterizerFactory;
use Atelier\Rasterizer\RasterizerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Rasterizer::class)]
final class RasterizerTest extends TestCase
{
    private string $originalPath = '';

    protected function setUp(): void
    {
        $path = getenv('PATH');
        $this->originalPath = false === $path ? '' : $path;

        RasterizerFactory::resetSupportCache();
    }

    protected function tearDown(): void
    {
        putenv('PATH='.$this->originalPath);
        RasterizerFactory::resetSupportCache();
    }

    public function testCreateDelegatesToDefaultFactorySelection(): void
    {
        $directory = sys_get_temp_dir().'/atelier-rasterizer-facade-'.bin2hex(random_bytes(4));
        $binaryPath = $directory.'/resvg';

        mkdir($directory);
        file_put_contents($binaryPath, "#!/bin/sh\nexit 0\n");
        chmod($binaryPath, 0755);

        try {
            putenv('PATH='.$directory);

            self::assertInstanceOf(ResvgRasterizer::class, Rasterizer::create());
        } finally {
            @unlink($binaryPath);
            @rmdir($directory);
        }
    }

    public function testExplicitResvgAdapter(): void
    {
        self::assertInstanceOf(ResvgRasterizer::class, Rasterizer::resvg());
        self::assertInstanceOf(RasterizerInterface::class, Rasterizer::resvg());
    }

    public function testExplicitRsvgConvertAdapter(): void
    {
        self::assertInstanceOf(RsvgConvertRasterizer::class, Rasterizer::rsvgConvert());
        self::assertInstanceOf(RasterizerInterface::class, Rasterizer::rsvgConvert());
    }

    public function testConstructorIsPrivate(): void
    {
        $constructor = (new \ReflectionClass(Rasterizer::class))->getConstructor();

        self::assertNotNull($constructor);
        self::assertTrue($constructor->isPrivate());

        $instance = (new \ReflectionClass(Rasterizer::class))->newInstanceWithoutConstructor();
        $constructor->invoke($instance);

        self::assertInstanceOf(Rasterizer::class, $instance);
    }
}
