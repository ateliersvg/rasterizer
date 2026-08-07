<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests\Double;

use Atelier\Rasterizer\Process\ProcessRunnerInterface;

final class TraceableRunner implements ProcessRunnerInterface
{
    /**
     * @var list<list<string>>
     */
    public array $commands = [];

    /**
     * @var list<float>
     */
    public array $timeouts = [];

    public function __construct(
        private readonly ProcessRunnerInterface $inner,
    ) {
    }

    public function run(array $command, float $timeout = 30.0): void
    {
        $this->commands[] = $command;
        $this->timeouts[] = $timeout;

        $this->inner->run($command, $timeout);
    }
}
