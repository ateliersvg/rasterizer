<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests;

use Atelier\Rasterizer\Adapter\RasterizerAdapterFactoryInterface;
use Atelier\Rasterizer\Adapter\ResvgRasterizerFactory;
use Atelier\Rasterizer\Adapter\RsvgConvertRasterizerFactory;
use Atelier\Rasterizer\Exception\ExceptionInterface;
use Atelier\Rasterizer\Exception\NoRasterizerAvailableException;
use Atelier\Rasterizer\RasterizerFactory;
use Atelier\Rasterizer\Tests\Double\ArrayLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RasterizerFactory::class)]
final class RasterizerFactoryTest extends TestCase
{
    protected function tearDown(): void
    {
        RasterizerFactory::resetSupportCache();
        self::resetDefaultAdapterFactoriesCache();
    }

    public function testSelectsFirstSupportedAdapterFactory(): void
    {
        $unsupported = new TestAdapterFactory('unsupported', false);
        $selected = new TestAdapterFactory('selected', true);
        $ignored = new TestAdapterFactory('ignored', true);

        $rasterizer = (new RasterizerFactory([$unsupported, $selected, $ignored]))->create();

        self::assertSame($selected->rasterizer, $rasterizer);
        self::assertSame(1, $unsupported->supportsCalls);
        self::assertSame(1, $selected->supportsCalls);
        self::assertSame(0, $ignored->supportsCalls);
        self::assertSame(1, $selected->createCalls);
    }

    public function testThrowsWhenNoAdapterFactoryIsSupported(): void
    {
        $this->expectException(NoRasterizerAvailableException::class);
        $this->expectExceptionMessage('resvg');

        (new RasterizerFactory([
            new TestAdapterFactory('resvg', false),
            new TestAdapterFactory('rsvg-convert', false),
        ]))->create();
    }

    public function testCachesSupportChecksUntilReset(): void
    {
        $adapterFactory = new TestAdapterFactory('resvg', false);
        $factory = new RasterizerFactory([$adapterFactory]);

        try {
            $factory->create();
        } catch (NoRasterizerAvailableException) {
        }

        $adapterFactory->supported = true;

        try {
            $factory->create();
        } catch (NoRasterizerAvailableException) {
        }

        self::assertSame(1, $adapterFactory->supportsCalls);

        RasterizerFactory::resetSupportCache();

        self::assertSame($adapterFactory->rasterizer, $factory->create());
        self::assertSame(2, $adapterFactory->supportsCalls);
    }

    public function testBuildsDefaultAdapterFactoriesWithoutLoggerAndMemoizesThem(): void
    {
        self::resetDefaultAdapterFactoriesCache();

        $first = self::adapterFactories(new RasterizerFactory());
        $second = self::adapterFactories(new RasterizerFactory());

        self::assertCount(2, $first);
        self::assertInstanceOf(ResvgRasterizerFactory::class, $first[0]);
        self::assertInstanceOf(RsvgConvertRasterizerFactory::class, $first[1]);
        self::assertSame($first, $second, 'No-logger defaults are memoized across instances.');
    }

    public function testBuildsFreshDefaultAdapterFactoriesWithLogger(): void
    {
        self::resetDefaultAdapterFactoriesCache();

        $logger = new ArrayLogger();

        $withLogger = self::adapterFactories(new RasterizerFactory(logger: $logger));
        $withoutLogger = self::adapterFactories(new RasterizerFactory());

        self::assertCount(2, $withLogger);
        self::assertInstanceOf(ResvgRasterizerFactory::class, $withLogger[0]);
        self::assertInstanceOf(RsvgConvertRasterizerFactory::class, $withLogger[1]);
        self::assertNotSame($withoutLogger, $withLogger, 'A logger bypasses the memoized defaults.');
    }

    public function testRejectsInvalidAdapterFactoriesWithPackageException(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->expectExceptionMessage('Adapter factory must implement');

        new RasterizerFactory([new \stdClass()]);
    }

    /**
     * @return list<RasterizerAdapterFactoryInterface>
     */
    private static function adapterFactories(RasterizerFactory $factory): array
    {
        $property = new \ReflectionProperty(RasterizerFactory::class, 'adapterFactories');

        /** @var list<RasterizerAdapterFactoryInterface> $value */
        $value = $property->getValue($factory);

        return $value;
    }

    private static function resetDefaultAdapterFactoriesCache(): void
    {
        $property = new \ReflectionProperty(RasterizerFactory::class, 'defaultAdapterFactories');
        $property->setValue(null, null);
    }
}
