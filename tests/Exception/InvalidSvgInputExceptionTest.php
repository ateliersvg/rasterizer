<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests\Exception;

use Atelier\Rasterizer\Exception\ExceptionInterface;
use Atelier\Rasterizer\Exception\InvalidSvgInputException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(InvalidSvgInputException::class)]
final class InvalidSvgInputExceptionTest extends TestCase
{
    public function testEmpty(): void
    {
        self::assertInstanceOf(ExceptionInterface::class, InvalidSvgInputException::empty());
    }

    public function testNotSvg(): void
    {
        self::assertStringContainsString('<svg>', InvalidSvgInputException::notSvg()->getMessage());
    }

    public function testFileNotReadableNamesThePath(): void
    {
        self::assertStringContainsString('/tmp/missing.svg', InvalidSvgInputException::fileNotReadable('/tmp/missing.svg')->getMessage());
    }

    public function testStreamNotReadable(): void
    {
        self::assertInstanceOf(ExceptionInterface::class, InvalidSvgInputException::streamNotReadable());
    }
}
