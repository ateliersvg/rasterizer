<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Tests\Double;

use Psr\Log\AbstractLogger;

final class ArrayLogger extends AbstractLogger
{
    /**
     * @var list<array{level: mixed, message: string|\Stringable, context: array<string, mixed>}>
     */
    public array $records = [];

    /**
     * @param array<string, mixed> $context
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $this->records[] = [
            'level' => $level,
            'message' => $message,
            'context' => $context,
        ];
    }

    public function hasRecord(string $message): bool
    {
        foreach ($this->records as $record) {
            if ($message === (string) $record['message']) {
                return true;
            }
        }

        return false;
    }
}
