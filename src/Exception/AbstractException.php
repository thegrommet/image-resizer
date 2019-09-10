<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Exception;

/**
 * Abstract exception
 */
abstract class AbstractException extends \Exception
{
    const CODE_BAD_REQUEST    = 400;
    const CODE_FORBIDDEN      = 403;
    const CODE_NOT_FOUND      = 404;
    const CODE_UNPROCESSABLE  = 422;
    const CODE_INTERNAL_ERROR = 500;
}
