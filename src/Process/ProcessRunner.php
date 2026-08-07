<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Process;

use Atelier\Rasterizer\Exception\RasterizationFailedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

/**
 * Runs a rasterizer command through Symfony Process and normalizes timeouts and
 * non-zero exits into the library's exception hierarchy.
 */
final class ProcessRunner implements ProcessRunnerInterface
{
    public function __construct(
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    /**
     * Runs a command and throws when it times out or exits unsuccessfully.
     *
     * @param list<string> $command
     */
    public function run(array $command, float $timeout = 30.0): void
    {
        $this->logger?->debug('Starting rasterizer command.', [
            'command' => $command,
            'timeout' => $timeout,
        ]);

        $process = new Process($command);
        $process->setTimeout($timeout);

        try {
            $process->run();
        } catch (ProcessTimedOutException $exception) {
            $this->logger?->warning('Rasterizer command timed out.', [
                'command' => $command,
                'timeout' => $timeout,
            ]);

            throw RasterizationFailedException::timedOut($command, $timeout, $exception);
        }

        if (!$process->isSuccessful()) {
            $this->logger?->error('Rasterizer command failed.', [
                'command' => $command,
                'exit_code' => $process->getExitCode(),
                'stderr' => $process->getErrorOutput(),
            ]);

            throw RasterizationFailedException::fromProcess($command, $process->getExitCode(), $process->getErrorOutput());
        }

        $this->logger?->debug('Rasterizer command completed.', [
            'command' => $command,
            'exit_code' => $process->getExitCode(),
        ]);
    }
}
