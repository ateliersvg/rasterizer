<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests\Exception;

use Atelier\Rasterizer\Exception\ExceptionInterface;
use Atelier\Rasterizer\Exception\RasterizationFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RasterizationFailedException::class)]
final class RasterizationFailedExceptionTest extends TestCase
{
    public function testFromProcessIncludesCommandAndExitCode(): void
    {
        $exception = RasterizationFailedException::fromProcess(['resvg', 'in.svg'], 1, '');

        self::assertInstanceOf(ExceptionInterface::class, $exception);
        self::assertStringContainsString('resvg in.svg', $exception->getMessage());
        self::assertStringContainsString('1', $exception->getMessage());
    }

    public function testFromProcessAppendsErrorOutputWhenPresent(): void
    {
        $exception = RasterizationFailedException::fromProcess(['resvg'], null, "  parse error\n");

        self::assertStringContainsString('unknown', $exception->getMessage());
        self::assertStringContainsString('parse error', $exception->getMessage());
    }

    public function testTimedOutCarriesThePreviousException(): void
    {
        $previous = new \RuntimeException('killed');
        $exception = RasterizationFailedException::timedOut(['resvg'], 30.0, $previous);

        self::assertStringContainsString('timed out', $exception->getMessage());
        self::assertSame($previous, $exception->getPrevious());
    }

    public function testEmptyOutput(): void
    {
        self::assertStringContainsString('resvg', RasterizationFailedException::emptyOutput('resvg')->getMessage());
    }

    public function testCannotCreateTemporaryFile(): void
    {
        self::assertInstanceOf(ExceptionInterface::class, RasterizationFailedException::cannotCreateTemporaryFile());
    }

    public function testCannotWriteNamesThePath(): void
    {
        self::assertStringContainsString('card.png', RasterizationFailedException::cannotWrite('card.png')->getMessage());
    }
}
