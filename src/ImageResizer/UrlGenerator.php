<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\ImageResizer;

use Grommet\ImageResizer\Exception\InvalidStrategy;
use Grommet\ImageResizer\Strategy;
use Grommet\ImageResizer\Strategy\AbstractStrategy;
use Grommet\ImageResizer\Strategy\Fit;
use Grommet\ImageResizer\Strategy\Optimize;
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

    public function __construct(string $baseUrl = '')
    {
        $this->baseUrl = $baseUrl;
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
        if (isset($resizeParams['strategy'])) {
            if (is_string($resizeParams['strategy'])) {
                $this->strategy = Strategy::factory($resizeParams['strategy'], $resizeParams);
            } elseif ($resizeParams['strategy'] instanceof StrategyInterface) {
                $this->strategy = $resizeParams['strategy'];
            } else {
                throw new InvalidStrategy('Invalid resize strategy', InvalidStrategy::CODE_UNPROCESSABLE);
            }
        } else {  // guess strategy
            if (isset($resizeParams['width']) || isset($resizeParams['height'])) {
                $this->strategy = new Fit();
            } else {
                $this->strategy = new Optimize();
            }
            $this->strategy->bindConfig($resizeParams);
        }
        return sprintf(
            '%s/%s/%s',
            rtrim($this->baseUrl, '/'),
            $this->strategy,
            ltrim($imagePath, '/')
        );
    }

    public function setStrategy(StrategyInterface $strategy): self
    {
        $this->strategy = $strategy;
        return $this;
    }
}
