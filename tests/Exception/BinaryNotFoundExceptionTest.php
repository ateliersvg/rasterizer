<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests\Exception;

use Atelier\Rasterizer\Exception\BinaryNotFoundException;
use Atelier\Rasterizer\Exception\ExceptionInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BinaryNotFoundException::class)]
final class BinaryNotFoundExceptionTest extends TestCase
{
    public function testForNamesTheMissingBinary(): void
    {
        $exception = BinaryNotFoundException::for('resvg');

        self::assertInstanceOf(ExceptionInterface::class, $exception);
        self::assertStringContainsString('resvg', $exception->getMessage());
    }
}
