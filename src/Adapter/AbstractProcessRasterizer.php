<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Adapter;

use Atelier\Rasterizer\Bitmap\BitmapFormat;
use Atelier\Rasterizer\Bitmap\BitmapOptions;
use Atelier\Rasterizer\Bitmap\BitmapResult;
use Atelier\Rasterizer\Exception\RasterizationFailedException;
use Atelier\Rasterizer\Exception\UnsupportedFormatException;
use Atelier\Rasterizer\Process\BinaryResolver;
use Atelier\Rasterizer\Process\ProcessRunner;
use Atelier\Rasterizer\Process\ProcessRunnerInterface;
use Atelier\Rasterizer\RasterizerInterface;
use Atelier\Rasterizer\Svg\SvgInput;
use Psr\Log\LoggerInterface;

/**
 * Base adapter for rasterizers that shell out to an external binary.
 *
 * It owns the shared workflow: validate the requested format, resolve the
 * binary, write the SVG to a temporary file, run the command, read the produced
 * bitmap, and clean up. Concrete adapters supply only the binary name, the
 * formats they can emit, and the command to build.
 */
abstract class AbstractProcessRasterizer implements RasterizerInterface
{
    protected readonly ProcessRunnerInterface $processRunner;

    public function __construct(
        protected readonly BinaryResolver $binaryResolver = new BinaryResolver(),
        ?ProcessRunnerInterface $processRunner = null,
        protected readonly ?string $binaryPath = null,
        protected readonly ?string $temporaryDirectory = null,
        ?LoggerInterface $logger = null,
    ) {
        $this->processRunner = $processRunner ?? new ProcessRunner($logger);
    }

    final public function rasterize(SvgInput|\Stringable|string $svg, BitmapOptions $options = new BitmapOptions()): BitmapResult
    {
        if (!\in_array($options->format, $this->supportedFormats(), true)) {
            throw UnsupportedFormatException::for($options->format, $this->binaryName(), $this->supportedFormats());
        }

        $input = $svg instanceof SvgInput ? $svg : SvgInput::fromString($svg);
        $binary = $this->binaryResolver->resolve($this->binaryName(), $this->binaryPath);
        $inputPath = $this->createTemporaryFile('svg', $input->markup);
        $outputPath = $this->createTemporaryFile($options->format->extension());

        try {
            $this->processRunner->run(
                $this->buildCommand($binary, $inputPath, $outputPath, $options),
                $options->timeout,
            );

            $contents = @file_get_contents($outputPath);

            if (false === $contents || '' === $contents) {
                throw RasterizationFailedException::emptyOutput($this->binaryName());
            }

            return new BitmapResult(
                contents: $contents,
                format: $options->format,
                width: $this->bitmapWidth($contents, $options),
                height: $this->bitmapHeight($contents, $options),
                mimeType: $options->format->mimeType(),
            );
        } finally {
            @unlink($inputPath);
            @unlink($outputPath);
        }
    }

    /**
     * The executable name looked up in PATH (for example "resvg").
     */
    abstract protected function binaryName(): string;

    /**
     * Formats this adapter's binary can produce.
     *
     * @return list<BitmapFormat>
     */
    abstract protected function supportedFormats(): array;

    /**
     * Builds the process command for the resolved binary and temporary paths.
     *
     * @return list<string>
     */
    abstract protected function buildCommand(string $binary, string $inputPath, string $outputPath, BitmapOptions $options): array;

    /**
     * Atomically creates a uniquely named temporary file and returns its path.
     */
    private function createTemporaryFile(string $extension, string $contents = ''): string
    {
        $directory = $this->temporaryDirectory ?? sys_get_temp_dir();
        $path = rtrim($directory, \DIRECTORY_SEPARATOR).\DIRECTORY_SEPARATOR.'atelier-rasterizer-'.bin2hex(random_bytes(8)).'.'.$extension;
        $handle = @fopen($path, 'x');

        if (false === $handle) {
            throw RasterizationFailedException::cannotCreateTemporaryFile();
        }

        if ('' !== $contents) {
            fwrite($handle, $contents);
        }

        fclose($handle);

        return $path;
    }

    private function bitmapWidth(string $contents, BitmapOptions $options): ?int
    {
        if (BitmapFormat::Png !== $options->format || \strlen($contents) < 24) {
            return $options->width;
        }

        $width = unpack('Nwidth', substr($contents, 16, 4));
        $value = \is_array($width) ? ($width['width'] ?? null) : null;

        return \is_int($value) ? $value : $options->width;
    }

    private function bitmapHeight(string $contents, BitmapOptions $options): ?int
    {
        if (BitmapFormat::Png !== $options->format || \strlen($contents) < 24) {
            return $options->height;
        }

        $height = unpack('Nheight', substr($contents, 20, 4));
        $value = \is_array($height) ? ($height['height'] ?? null) : null;

        return \is_int($value) ? $value : $options->height;
    }
}
