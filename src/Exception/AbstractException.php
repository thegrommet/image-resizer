<?php

declare(strict_types=1);

namespace Grommet\ImageResizer\Exception;

/**
 * Abstract exception
 */
abstract class AbstractException extends \Exception
{
    public const CODE_BAD_REQUEST    = 400;
    public const CODE_FORBIDDEN      = 403;
    public const CODE_NOT_FOUND      = 404;
    public const CODE_UNPROCESSABLE  = 422;
    public const CODE_INTERNAL_ERROR = 500;
}
