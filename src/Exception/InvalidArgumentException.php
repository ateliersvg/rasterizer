<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Exception;

/**
 * Thrown when a public API receives an invalid argument.
 */
final class InvalidArgumentException extends \InvalidArgumentException implements ExceptionInterface
{
}
