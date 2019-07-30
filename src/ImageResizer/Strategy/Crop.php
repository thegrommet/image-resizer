<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Strategy;

/**
 * Crop resize strategy - resize the image to fit the specified bounds while preserving the aspect ratio
 */
class Crop extends AbstractStrategy
{
    const STRATEGY = 'crop';

    const CROP_MODE_CENTER = 'c';
    const CROP_MODE_TOP = 't';
    const CROP_MODE_BOTTOM = 'b';
    const CROP_MODE_LEFT = 'l';
    const CROP_MODE_RIGHT = 'r';

    /**
     * @var string
     */
    public $cropMode = self::CROP_MODE_CENTER;

    public function __construct(int $width = null, int $height = null, string $cropMode = self::CROP_MODE_CENTER)
    {
        parent::__construct($width, $height);
        $this->cropMode = $cropMode;
    }

    public function validate(): bool
    {
        return $this->width > 0 && $this->height > 0 && !empty($this->cropMode);
    }
}
