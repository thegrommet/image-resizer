<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Adapter;

use Grommet\ImageResizer\Exception\InvalidStrategy;
use Grommet\ImageResizer\Exception\ResizeException;
use Grommet\ImageResizer\Strategy\Crop;
use Grommet\ImageResizer\Strategy\Exact;
use Grommet\ImageResizer\Strategy\Fit;
use Grommet\ImageResizer\Strategy\StrategyInterface;
use Gumlet\ImageResize;
use Gumlet\ImageResizeException;

/**
 * Local, PHP-based image resize adapter
 */
class Local implements AdapterInterface
{
    /**
     * @var ImageResize
     */
    private $resizer;

    /**
     * @var StrategyInterface
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
            $this->resizer->quality_jpg = 85;
            $this->resizer->quality_png = 6;
            $this->resizer->gamma_correct = false;
        }
        $this->strategy = $strategy;

        if ($strategy instanceof Fit) {
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

    public function setResizer(ImageResize $resizer): self
    {
        $this->resizer = $resizer;
        return $this;
    }

    public function supportedStrategies(): array
    {
        return [
            Exact::STRATEGY,
            Fit::STRATEGY,
            Crop::STRATEGY
        ];
    }
}
