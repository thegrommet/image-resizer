<?php

declare(strict_types=1);

namespace Grommet\ImageResizer\Strategy;

/**
 * Crop resize strategy - resize the image to fit the specified bounds while preserving the aspect ratio
 */
class Crop extends AbstractStrategy
{
    public const STRATEGY = 'crop';

    public const CROP_MODE_CENTER = 'c';
    public const CROP_MODE_TOP = 't';
    public const CROP_MODE_BOTTOM = 'b';
    public const CROP_MODE_LEFT = 'l';
    public const CROP_MODE_RIGHT = 'r';

    /**
     * @var string
     */
    public $cropMode = self::CROP_MODE_CENTER;

    public function __construct(
        int $width = null,
        int $height = null,
        int $quality = null,
        string $cropMode = self::CROP_MODE_CENTER
    ) {
        parent::__construct($width, $height, $quality);
        $this->configAliases['m'] = 'cropMode';
        $this->cropMode = $cropMode;
    }

    public function validate(): bool
    {
        return $this->width > 0 && $this->height > 0 && !empty($this->cropMode);
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), ['m' => $this->cropMode]);
    }
}
