<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Adapter;

use Grommet\ImageResizer\Adapter\Gumlet\ImageResize;
use Grommet\ImageResizer\Exception\InvalidArgument;
use Grommet\ImageResizer\Exception\InvalidStrategy;
use Grommet\ImageResizer\Exception\ResizeException;
use Grommet\ImageResizer\Strategy\AbstractStrategy;
use Grommet\ImageResizer\Strategy\Crop;
use Grommet\ImageResizer\Strategy\Exact;
use Grommet\ImageResizer\Strategy\Fill;
use Grommet\ImageResizer\Strategy\Fit;
use Grommet\ImageResizer\Strategy\Optimize;
use Grommet\ImageResizer\Strategy\StrategyInterface;
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
    public $defaultPngQuality = 7;

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
        try {
            $this->resizer = new ImageResize($source);
        } catch (ImageResizeException $e) {
            throw new ResizeException($e->getMessage(), ResizeException::CODE_NOT_FOUND, $e);
        }
        $this->resizer->gamma_correct = false;
        if ($strategy->quality) {
            $this->resizer->quality_jpg = $this->normalizeJpgQuality($strategy->quality);
            $this->resizer->quality_png = $this->normalizePngQuality($strategy->quality);
        } else {
            $this->resizer->quality_jpg = $this->defaultJpgQuality;
            $this->resizer->quality_png = $this->defaultPngQuality;
        }
        $this->strategy = $strategy;

        if ($strategy instanceof Optimize) {
            $this->resizer->scale(100);
        } elseif ($strategy instanceof Fit) {
            $this->fit();
        } elseif ($strategy instanceof Fill) {
            $this->fill();
        } elseif ($strategy instanceof Exact) {
            $this->resizer->resize($strategy->width, $strategy->height, true);
        } elseif ($strategy instanceof Crop) {
            $this->crop();
        } else {
            throw new InvalidStrategy('Strategy not supported by this adapter', InvalidStrategy::CODE_UNPROCESSABLE);
        }
        try {
            $this->resizer->save($destination);
        } catch (\Exception $e) {
            throw new ResizeException($e->getMessage(), ResizeException::CODE_INTERNAL_ERROR, $e);
        }
        if (!file_exists($destination)) {
            throw new ResizeException('Could not resize image', ResizeException::CODE_INTERNAL_ERROR);
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
            Fill::STRATEGY,
            Fit::STRATEGY,
            Crop::STRATEGY
        ];
    }

    private function fit(): void
    {
        if ($this->strategy->width && $this->strategy->height) {
            $this->resizer->resizeToBestFit($this->strategy->width, $this->strategy->height, false);
        } elseif ($this->strategy->width) {
            $this->resizer->resizeToWidth($this->strategy->width, false);
        } else {
            $this->resizer->resizeToHeight($this->strategy->height, false);
        }
    }

    private function fill(): void
    {
        $this->resizer->setBackground(...$this->hexToRgb($this->strategy->background));
        $this->resizer->resizeToFill($this->strategy->width, $this->strategy->height, true);
    }

    private function crop(): void
    {
        switch ($this->strategy->cropMode) {
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
        $this->resizer->crop($this->strategy->width, $this->strategy->height, false, $cropMode);
    }

    /**
     * @link https://www.php.net/manual/en/function.hexdec.php
     */
    private function hexToRgb(string $hex): array
    {
        $hex = preg_replace('/[^0-9A-Fa-f]/', '', $hex); // Gets a proper hex string
        $rgb = [];
        if (strlen($hex) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
            $colorVal = hexdec($hex);
            $rgb[] = 0xFF & ($colorVal >> 0x10);
            $rgb[] = 0xFF & ($colorVal >> 0x8);
            $rgb[] = 0xFF & $colorVal;
        } elseif (strlen($hex) == 3) { //if shorthand notation, need some string manipulations
            $rgb[] = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $rgb[] = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $rgb[] = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } else {
            throw new InvalidArgument('Invalid hex code');
        }
        return $rgb;
    }
}
