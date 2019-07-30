<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Strategy;

/**
 * Fill resize strategy - resize the image to fit the specified bounds while preserving the aspect ratio
 */
class Fill extends AbstractStrategy
{
    const STRATEGY = 'fill';

    /**
     * @var string
     */
    public $background = '#ffffff';

    public function __construct(int $width = null, int $height = null, string $background = '#ffffff')
    {
        parent::__construct($width, $height);
        $this->background = $background;
    }

    public function validate(): bool
    {
        return $this->width > 0 || $this->height > 0;
    }
}
