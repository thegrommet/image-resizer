<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Adapter;

use Grommet\ImageResizer\Exception\InvalidStrategy;
use Grommet\ImageResizer\Exception\ResizeException;
use Grommet\ImageResizer\Strategy\Crop;
use Grommet\ImageResizer\Strategy\Exact;
use Grommet\ImageResizer\Strategy\Fill;
use Grommet\ImageResizer\Strategy\Fit;
use Grommet\ImageResizer\Strategy\Optimize;
use Grommet\ImageResizer\Strategy\StrategyInterface;

/**
 * Kraken resize adapter
 */
class Kraken implements AdapterInterface
{
    /**
     * @var int
     */
    public $defaultQuality = 80;

    private $apiKey;
    private $apiSecret;

    public function __construct(string $apiKey, string $apiSecret)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    public function resize(string $source, string $destination, StrategyInterface $strategy): bool
    {
        if (!is_readable($source)) {
            throw new ResizeException('Image source file does not exist', ResizeException::CODE_NOT_FOUND);
        }
        $kraken = new \Kraken($this->apiKey, $this->apiSecret);
        $params = array_merge([
            'file' => $source,
            'wait' => true,
            'lossy' => true
        ], $this->strategyToConfig($strategy));
        try {
            $data = $kraken->upload($params);
        } catch (\Exception $e) {
            throw new ResizeException($e->getMessage(), ResizeException::CODE_INTERNAL_ERROR, $e);
        }
        if ($data['success'] && !empty($data['kraked_url'])) {
            try {
                file_put_contents($destination, fopen($data['kraked_url'], 'rb'));
            } catch (\Exception $e) {
                throw new ResizeException($e->getMessage(), ResizeException::CODE_INTERNAL_ERROR, $e);
            }
        } else {
            throw new ResizeException($data['message'] ?? 'Resize failure', ResizeException::CODE_INTERNAL_ERROR);
        }
        if (!file_exists($destination)) {
            throw new ResizeException('Could not resize image', ResizeException::CODE_INTERNAL_ERROR);
        }
        return true;
    }

    public function supportedStrategies(): array
    {
        return [
            Optimize::STRATEGY,
            Exact::STRATEGY,
            Fill::STRATEGY,
            Fit::STRATEGY,
            Crop::STRATEGY
        ];
    }

    /**
     * Convert a strategy to Kraken config
     *
     * @param StrategyInterface $strategy
     * @return array
     */
    private function strategyToConfig(StrategyInterface $strategy): array
    {
        if (!in_array($strategy->name(), $this->supportedStrategies())) {
            throw new InvalidStrategy('Strategy not supported by this adapter', InvalidStrategy::CODE_UNPROCESSABLE);
        }
        $config = ['quality' => $strategy->quality ?: $this->defaultQuality];
        if ($strategy instanceof Optimize) {
            return $config;
        }
        $config['resize'] = [
            'strategy' => $strategy->name(),
            'width' => $strategy->width,
            'height' => $strategy->height
        ];
        if ($strategy instanceof Fill) {
            $config['resize']['background'] = $strategy->background;
        } elseif ($strategy instanceof Crop) {
            $config['resize']['crop_mode'] = $strategy->cropMode;
        }
        return $config;
    }
}
