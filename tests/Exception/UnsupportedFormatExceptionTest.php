<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests\Exception;

use Atelier\Rasterizer\Bitmap\BitmapFormat;
use Atelier\Rasterizer\Exception\ExceptionInterface;
use Atelier\Rasterizer\Exception\UnsupportedFormatException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UnsupportedFormatException::class)]
final class UnsupportedFormatExceptionTest extends TestCase
{
    public function testForListsBinaryAndSupportedFormats(): void
    {
        $exception = UnsupportedFormatException::for(BitmapFormat::Jpeg, 'resvg', [BitmapFormat::Png]);

        self::assertInstanceOf(ExceptionInterface::class, $exception);
        self::assertStringContainsString('resvg', $exception->getMessage());
        self::assertStringContainsString('jpg', $exception->getMessage());
        self::assertStringContainsString('png', $exception->getMessage());
    }
}
