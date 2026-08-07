<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests\Process;

use Atelier\Rasterizer\Exception\RasterizationFailedException;
use Atelier\Rasterizer\Process\ProcessRunner;
use Atelier\Rasterizer\Tests\Double\ArrayLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProcessRunner::class)]
final class ProcessRunnerTest extends TestCase
{
    public function testRunSucceedsForZeroExit(): void
    {
        $logger = new ArrayLogger();

        (new ProcessRunner($logger))->run([\PHP_BINARY, '-r', 'exit(0);']);

        self::assertTrue($logger->hasRecord('Starting rasterizer command.'));
        self::assertTrue($logger->hasRecord('Rasterizer command completed.'));
    }

    public function testRunThrowsForNonZeroExit(): void
    {
        $logger = new ArrayLogger();

        $this->expectException(RasterizationFailedException::class);

        try {
            (new ProcessRunner($logger))->run([\PHP_BINARY, '-r', 'fwrite(STDERR, "boom"); exit(3);']);
        } finally {
            self::assertTrue($logger->hasRecord('Rasterizer command failed.'));
        }
    }

    public function testRunThrowsWhenCommandTimesOut(): void
    {
        $logger = new ArrayLogger();

        $this->expectException(RasterizationFailedException::class);
        $this->expectExceptionMessage('timed out');

        try {
            (new ProcessRunner($logger))->run([\PHP_BINARY, '-r', 'usleep(2_000_000);'], 0.1);
        } finally {
            self::assertTrue($logger->hasRecord('Rasterizer command timed out.'));
        }
    }
}
