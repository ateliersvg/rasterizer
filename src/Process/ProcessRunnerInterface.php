<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Process;

/**
 * Runs an external rasterizer command and normalizes process failures.
 */
interface ProcessRunnerInterface
{
    /**
     * Runs a command with a timeout.
     *
     * @param list<string> $command
     */
    public function run(array $command, float $timeout = 30.0): void;
}
