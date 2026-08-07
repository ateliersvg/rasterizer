<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests\Exception;

use Atelier\Rasterizer\Exception\ExceptionInterface;
use Atelier\Rasterizer\Exception\NoRasterizerAvailableException;
use Atelier\Rasterizer\Tests\TestAdapterFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NoRasterizerAvailableException::class)]
final class NoRasterizerAvailableExceptionTest extends TestCase
{
    public function testForAdapterFactoriesNamesAttemptedAdapters(): void
    {
        $exception = NoRasterizerAvailableException::forAdapterFactories([
            new TestAdapterFactory('resvg', false),
            new TestAdapterFactory('rsvg-convert', false),
        ]);

        self::assertInstanceOf(ExceptionInterface::class, $exception);
        self::assertStringContainsString('resvg', $exception->getMessage());
        self::assertStringContainsString('rsvg-convert', $exception->getMessage());
    }
}
