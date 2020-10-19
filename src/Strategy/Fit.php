<?php

declare(strict_types=1);

namespace Grommet\ImageResizer\Strategy;

/**
 * Fit resize strategy - crop and resize the image to fit the desired width and height
 */
class Fit extends AbstractStrategy
{
    public const STRATEGY = 'fit';

    public function validate(): bool
    {
        return $this->width > 0 || $this->height > 0;
    }
}
