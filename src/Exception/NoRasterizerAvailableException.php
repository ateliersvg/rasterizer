<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Exception;

use Atelier\Rasterizer\Adapter\RasterizerAdapterFactoryInterface;

/**
 * Thrown when no configured rasterizer adapter is available.
 */
final class NoRasterizerAvailableException extends \RuntimeException implements ExceptionInterface
{
    /**
     * @param iterable<RasterizerAdapterFactoryInterface> $adapterFactories
     */
    public static function forAdapterFactories(iterable $adapterFactories): self
    {
        $names = [];

        foreach ($adapterFactories as $adapterFactory) {
            $names[] = $adapterFactory->name();
        }

        $suffix = [] === $names ? 'No adapter factories were configured.' : \sprintf('Install one of: %s.', implode(', ', $names));

        return new self(\sprintf('No supported rasterizer adapter is available. %s', $suffix));
    }
}
