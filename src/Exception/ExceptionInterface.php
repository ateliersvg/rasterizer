<?php

declare(strict_types=1);

namespace Atelier\Rasterizer\Exception;

/**
 * Implemented by every exception thrown by this library, so callers can catch
 * all rasterizer failures with a single type.
 */
interface ExceptionInterface extends \Throwable
{
}
