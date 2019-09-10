<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Strategy;

/**
 * Optimize strategy - doesn't resize but instead just saves to reduce file size
 */
class Optimize extends AbstractStrategy
{
    const STRATEGY = 'optimize';

    public function validate(): bool
    {
        return true;
    }
}
