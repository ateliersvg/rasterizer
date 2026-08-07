<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Adapter;

use Atelier\Rasterizer\Exception\BinaryNotFoundException;
use Atelier\Rasterizer\Process\BinaryResolver;
use Atelier\Rasterizer\Process\ProcessRunnerInterface;
use Atelier\Rasterizer\RasterizerInterface;
use Psr\Log\LoggerInterface;

/**
 * Creates rsvg-convert adapters when the rsvg-convert binary can be resolved.
 */
final readonly class RsvgConvertRasterizerFactory implements RasterizerAdapterFactoryInterface
{
    public function __construct(
        private BinaryResolver $binaryResolver = new BinaryResolver(),
        private ?ProcessRunnerInterface $processRunner = null,
        private ?string $binaryPath = null,
        private ?string $temporaryDirectory = null,
        private ?LoggerInterface $logger = null,
    ) {
    }

    public function name(): string
    {
        return 'rsvg-convert';
    }

    public function supports(): bool
    {
        try {
            $this->binaryResolver->resolve($this->name(), $this->binaryPath);

            return true;
        } catch (BinaryNotFoundException) {
            return false;
        }
    }

    public function create(): RasterizerInterface
    {
        return new RsvgConvertRasterizer(
            binaryResolver: $this->binaryResolver,
            processRunner: $this->processRunner,
            binaryPath: $this->binaryPath,
            temporaryDirectory: $this->temporaryDirectory,
            logger: $this->logger,
        );
    }
}
