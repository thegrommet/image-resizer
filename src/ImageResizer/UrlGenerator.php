<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\ImageResizer;

use Grommet\ImageResizer\Strategy;
use Grommet\ImageResizer\Strategy\AbstractStrategy;
use Grommet\ImageResizer\Strategy\StrategyInterface;

/**
 * Image URL generator
 */
class UrlGenerator
{
    /**
     * Base URL to where resized images are served
     *
     * @var string
     */
    public $baseUrl = '';

    /**
     * @var AbstractStrategy
     */
    private $strategy;

    /**
     * @var array
     */
    private $presets = [];

    public function __construct(string $baseUrl = '', array $presets = [])
    {
        $this->baseUrl = $baseUrl;
        foreach ($presets as $name => $params) {
            $this->addPreset($name, $params);
        }
    }

    /**
     * Generate a URL to the given image
     *
     * @param string $imagePath
     * @param array $resizeParams
     * @return string
     */
    public function imageUrl(string $imagePath, array $resizeParams = []): string
    {
        if (isset($resizeParams['size']) && isset($this->presets[$resizeParams['size']])) {
            $resizeParams = array_merge($this->presets[$resizeParams['size']], $resizeParams);
        }
        $this->strategy = Strategy::createFromConfig($resizeParams);
        return sprintf(
            '%s/%s/%s',
            rtrim($this->baseUrl, '/'),
            urlencode($this->strategy->__toString()),
            ltrim($imagePath, '/')
        );
    }

    public function setStrategy(StrategyInterface $strategy): self
    {
        $this->strategy = $strategy;
        return $this;
    }

    public function addPreset(string $name, array $resizeParams): self
    {
        $this->presets[$name] = $resizeParams;
        return $this;
    }
}
