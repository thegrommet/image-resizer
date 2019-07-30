<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Strategy;

/**
 * Abstract Resize Strategy
 */
abstract class AbstractStrategy implements StrategyInterface
{
    protected $configAliases = [
        'w' => 'width',
        'h' => 'height'
    ];

    /**
     * @var int
     */
    public $width;

    /**
     * @var int
     */
    public $height;

    public function __construct(int $width = null, int $height = null)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function bindConfig(array $config): void
    {
        foreach ($config as $key => $value) {
            if (array_key_exists($key, $this->configAliases)) {
                $key = $this->configAliases[$key];
            }
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
