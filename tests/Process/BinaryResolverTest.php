<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests\Process;

use Atelier\Rasterizer\Exception\BinaryNotFoundException;
use Atelier\Rasterizer\Process\BinaryResolver;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\ExecutableFinder;

#[CoversClass(BinaryResolver::class)]
final class BinaryResolverTest extends TestCase
{
    public function testReturnsConfiguredPathWithoutLookup(): void
    {
        $resolver = new BinaryResolver();

        self::assertSame('/opt/custom/resvg', $resolver->resolve('resvg', '/opt/custom/resvg'));
    }

    #[Group('system')]
    public function testResolvesDiscoverableBinaryFromPath(): void
    {
        $finder = new ExecutableFinder();

        if (null === $finder->find('rsvg-convert')) {
            self::markTestSkipped('rsvg-convert is not installed.');
        }

        $path = (new BinaryResolver())->resolve('rsvg-convert');

        self::assertFileExists($path);
    }

    public function testThrowsWhenBinaryIsMissing(): void
    {
        $this->expectException(BinaryNotFoundException::class);

        (new BinaryResolver())->resolve('atelier-rasterizer-missing-binary');
    }
}
