<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests\Svg;

use Atelier\Rasterizer\Exception\InvalidSvgInputException;
use Atelier\Rasterizer\Svg\SvgInput;
use Atelier\Svg\Svg;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SvgInput::class)]
final class SvgInputTest extends TestCase
{
    private const string MARKUP = '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"></svg>';

    public function testFromStringTrimsMarkup(): void
    {
        self::assertSame(self::MARKUP, SvgInput::fromString('  '.self::MARKUP."\n")->markup);
    }

    public function testFromStringAcceptsStringable(): void
    {
        $stringable = new readonly class(self::MARKUP) implements \Stringable {
            public function __construct(private string $markup)
            {
            }

            public function __toString(): string
            {
                return $this->markup;
            }
        };

        self::assertSame(self::MARKUP, SvgInput::fromString($stringable)->markup);
    }

    public function testFromStringAcceptsAtelierSvgDocument(): void
    {
        $svg = Svg::fromString(self::MARKUP);

        self::assertStringContainsString('<svg', SvgInput::fromString($svg)->markup);
    }

    public function testFromStringRejectsEmptyInput(): void
    {
        $this->expectException(InvalidSvgInputException::class);

        SvgInput::fromString('   ');
    }

    public function testFromStringRejectsNonSvgInput(): void
    {
        $this->expectException(InvalidSvgInputException::class);

        SvgInput::fromString('<html></html>');
    }

    public function testFromFileReadsMarkup(): void
    {
        $input = SvgInput::fromFile(__DIR__.'/../Fixtures/simple.svg');

        self::assertStringContainsString('<svg', $input->markup);
    }

    public function testFromFileAcceptsSplFileInfo(): void
    {
        $input = SvgInput::fromFile(new \SplFileInfo(__DIR__.'/../Fixtures/simple.svg'));

        self::assertStringContainsString('<svg', $input->markup);
    }

    public function testFromFileRejectsMissingFile(): void
    {
        $this->expectException(InvalidSvgInputException::class);

        SvgInput::fromFile('/atelier-rasterizer/does-not-exist.svg');
    }

    public function testFromStreamReadsMarkup(): void
    {
        $stream = fopen('php://temp', 'r+');
        self::assertIsResource($stream);
        fwrite($stream, self::MARKUP);
        rewind($stream);

        try {
            self::assertSame(self::MARKUP, SvgInput::fromStream($stream)->markup);
        } finally {
            fclose($stream);
        }
    }

    public function testFromStreamRejectsNonResource(): void
    {
        $this->expectException(InvalidSvgInputException::class);

        // Deliberately passing a non-resource.
        SvgInput::fromStream('not a stream');
    }
}
