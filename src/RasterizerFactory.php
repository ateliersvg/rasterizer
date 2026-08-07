<?php

declare(strict_types=1);

namespace Atelier\Rasterizer;

use Atelier\Rasterizer\Adapter\RasterizerAdapterFactoryInterface;
use Atelier\Rasterizer\Adapter\ResvgRasterizerFactory;
use Atelier\Rasterizer\Adapter\RsvgConvertRasterizerFactory;
use Atelier\Rasterizer\Exception\InvalidArgumentException;
use Atelier\Rasterizer\Exception\NoRasterizerAvailableException;
use Psr\Log\LoggerInterface;

/**
 * Selects the first supported rasterizer adapter factory.
 */
final class RasterizerFactory
{
    /**
     * @var array<string, bool>
     */
    private static array $supportCache = [];

    /**
     * @var list<RasterizerAdapterFactoryInterface>|null
     */
    private static ?array $defaultAdapterFactories = null;

    /**
     * @var list<RasterizerAdapterFactoryInterface>
     */
    private readonly array $adapterFactories;

    /**
     * @param iterable<RasterizerAdapterFactoryInterface>|null $adapterFactories
     */
    public function __construct(?iterable $adapterFactories = null, ?LoggerInterface $logger = null)
    {
        $this->adapterFactories = null === $adapterFactories ? self::defaultAdapterFactories($logger) : self::normalizeAdapterFactories($adapterFactories);
    }

    /**
     * Creates the first supported adapter from the configured factories.
     */
    public function create(): RasterizerInterface
    {
        foreach ($this->adapterFactories as $adapterFactory) {
            if ($this->supports($adapterFactory)) {
                return $adapterFactory->create();
            }
        }

        throw NoRasterizerAvailableException::forAdapterFactories($this->adapterFactories);
    }

    /**
     * Clears cached support checks, useful when PATH changes in a long process.
     */
    public static function resetSupportCache(): void
    {
        self::$supportCache = [];
    }

    private function supports(RasterizerAdapterFactoryInterface $adapterFactory): bool
    {
        $cacheKey = $this->supportCacheKey($adapterFactory);

        return self::$supportCache[$cacheKey] ??= $adapterFactory->supports();
    }

    private function supportCacheKey(RasterizerAdapterFactoryInterface $adapterFactory): string
    {
        return \sprintf('%d:%s', spl_object_id($adapterFactory), $adapterFactory->name());
    }

    /**
     * @return list<RasterizerAdapterFactoryInterface>
     */
    private static function defaultAdapterFactories(?LoggerInterface $logger): array
    {
        if (null === $logger) {
            return self::$defaultAdapterFactories ??= [
                new ResvgRasterizerFactory(),
                new RsvgConvertRasterizerFactory(),
            ];
        }

        return [
            new ResvgRasterizerFactory(logger: $logger),
            new RsvgConvertRasterizerFactory(logger: $logger),
        ];
    }

    /**
     * @param iterable<RasterizerAdapterFactoryInterface> $adapterFactories
     *
     * @return list<RasterizerAdapterFactoryInterface>
     */
    private static function normalizeAdapterFactories(iterable $adapterFactories): array
    {
        $normalized = [];

        foreach ($adapterFactories as $adapterFactory) {
            if (!$adapterFactory instanceof RasterizerAdapterFactoryInterface) {
                throw new InvalidArgumentException(\sprintf('Adapter factory must implement %s.', RasterizerAdapterFactoryInterface::class));
            }

            $normalized[] = $adapterFactory;
        }

        return $normalized;
    }
}
