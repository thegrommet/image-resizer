<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Adapter;

use Grommet\ImageResizer\Exception\InvalidStrategy;
use Grommet\ImageResizer\Exception\ResizeException;
use Grommet\ImageResizer\Strategy\AbstractStrategy;
use Grommet\ImageResizer\Strategy\Crop;
use Grommet\ImageResizer\Strategy\Exact;
use Grommet\ImageResizer\Strategy\Fit;
use Grommet\ImageResizer\Strategy\Optimize;
use Grommet\ImageResizer\Strategy\StrategyInterface;
use Gumlet\ImageResize;
use Gumlet\ImageResizeException;

/**
 * Local, PHP-based image resize adapter
 */
class Local implements AdapterInterface
{
    /**
     * @var int
     */
    public $defaultJpgQuality = 85;

    /**
     * @var int
     */
    public $defaultPngQuality = 6;

    /**
     * @var ImageResize
     */
    private $resizer;

    /**
     * @var AbstractStrategy
     */
    private $strategy;

    public function resize(string $source, string $destination, StrategyInterface $strategy): bool
    {
        if (!$this->resizer) {
            try {
                $this->resizer = new ImageResize($source);
            } catch (ImageResizeException $e) {
                throw new ResizeException($e->getMessage(), 0, $e);
            }
            $this->resizer->gamma_correct = false;
            if ($strategy->quality) {
                $this->resizer->quality_jpg = $this->normalizeJpgQuality($strategy->quality);
                $this->resizer->quality_png = $this->normalizePngQuality($strategy->quality);
            } else {
                $this->resizer->quality_jpg = $this->defaultJpgQuality;
                $this->resizer->quality_png = $this->defaultPngQuality;
            }
        }
        $this->strategy = $strategy;

        if ($strategy instanceof Optimize) {
            $this->resizer->scale(100);
        } elseif ($strategy instanceof Fit) {
            if ($strategy->width && $strategy->height) {
                $this->resizer->resizeToBestFit($strategy->width, $strategy->height, true);
            } elseif ($strategy->width) {
                $this->resizer->resizeToWidth($strategy->width, true);
            } else {
                $this->resizer->resizeToHeight($strategy->height, true);
            }
        } elseif ($strategy instanceof Exact) {
            $this->resizer->resize($strategy->width, $strategy->height, true);
        } elseif ($strategy instanceof Crop) {
            switch ($strategy->cropMode) {
                case Crop::CROP_MODE_TOP:
                    $cropMode = ImageResize::CROPTOPCENTER;
                    break;
                case Crop::CROP_MODE_BOTTOM:
                    $cropMode = ImageResize::CROPBOTTOM;
                    break;
                case Crop::CROP_MODE_LEFT:
                    $cropMode = ImageResize::CROPLEFT;
                    break;
                case Crop::CROP_MODE_RIGHT:
                    $cropMode = ImageResize::CROPRIGHT;
                    break;
                case Crop::CROP_MODE_CENTER:
                default:
                    $cropMode = ImageResize::CROPCENTER;
            }
            $this->resizer->crop($strategy->width, $strategy->height, true, $cropMode);
        } else {
            throw new InvalidStrategy('Strategy not supported by this adapter');
        }
        try {
            $this->resizer->save($destination);
        } catch (\Exception $e) {
            throw new ResizeException($e->getMessage(), 0, $e);
        }
        if (!file_exists($destination)) {
            throw new ResizeException('Could not resize image');
        }
        return true;
    }

    private function normalizeJpgQuality(int $quality): int
    {
        if ($quality > 0 && $quality <= 100) {
            return $quality;
        }
        return $this->defaultJpgQuality;
    }

    private function normalizePngQuality(int $quality): int
    {
        if ($quality < 1 || $quality > 100) {
            $quality = $this->defaultPngQuality * 10;
        }
        return (int)round($quality / 10);
    }

    public function setResizer(ImageResize $resizer): self
    {
        $this->resizer = $resizer;
        return $this;
    }

    public function supportedStrategies(): array
    {
        return [
            Optimize::STRATEGY,
            Exact::STRATEGY,
            Fit::STRATEGY,
            Crop::STRATEGY
        ];
    }
}
