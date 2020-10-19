<?php

declare(strict_types=1);

namespace Grommet\ImageResizer\Strategy;

/**
 * Exact resize strategy - resize to exact width and height, aspect ratio will not be maintained
 */
class Exact extends AbstractStrategy
{
    public const STRATEGY = 'exact';

    public function validate(): bool
    {
        return $this->width > 0 && $this->height > 0;
    }
}
