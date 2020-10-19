<?php

declare(strict_types=1);

namespace Grommet\ImageResizer\Strategy;

use Grommet\ImageResizer\Exception\InvalidStrategy;

/**
 * Abstract Resize Strategy
 */
abstract class AbstractStrategy implements StrategyInterface
{
    public const STRATEGY = '';

    protected $configAliases = [
        'w' => 'width',
        'h' => 'height',
        'q' => 'quality'
    ];

    protected $propertyTypes = [
        'width' => 'int',
        'height' => 'int',
        'quality' => 'int'
    ];

    /**
     * @var int
     */
    public $width;

    /**
     * @var int
     */
    public $height;

    /**
     * Quality setting, 0-100
     *
     * @var int
     */
    public $quality;

    public function __construct(int $width = null, int $height = null, int $quality = null)
    {
        $this->width = $width;
        $this->height = $height;
        $this->quality = $quality;
    }

    public function bindConfig(array $config): void
    {
        foreach ($config as $key => $value) {
            if (array_key_exists($key, $this->configAliases)) {
                $key = $this->configAliases[$key];
            }
            if (property_exists($this, $key)) {
                if (isset($this->propertyTypes[$key])) {
                    settype($value, $this->propertyTypes[$key]);
                }
                $this->$key = $value;
            }
        }
    }

    public function __toString(): string
    {
        if (!$this->validate()) {
            return '';
        }
        $properties = array_filter($this->toArray(), function ($property) {
            return !empty($property);
        });
        $flat = [];
        foreach ($properties as $key => $val) {
            $flat[] = $key . '-' . $val;
        }
        return trim(static::STRATEGY . '_' . implode('_', $flat), '_');
    }

    public function toArray(): array
    {
        return [
            'w' => $this->width,
            'h' => $this->height,
            'q' => $this->quality
        ];
    }

    public function name(): string
    {
        return static::STRATEGY;
    }
}
