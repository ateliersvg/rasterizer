<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Adapter;

use Atelier\Rasterizer\RasterizerInterface;

/**
 * Creates a rasterizer adapter when its binary is available.
 */
interface RasterizerAdapterFactoryInterface
{
    /**
     * Human-readable adapter name, usually the binary name.
     */
    public function name(): string;

    /**
     * Whether this adapter can be created in the current environment.
     */
    public function supports(): bool;

    /**
     * Creates the rasterizer adapter.
     */
    public function create(): RasterizerInterface;
}
