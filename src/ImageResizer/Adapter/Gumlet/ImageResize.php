<?php

namespace Grommet\ImageResizer\Adapter\Gumlet;

use Gumlet\ImageResize as BaseImageResize;
use Gumlet\ImageResizeException;

/**
 * Image Resize override to support bg fill
 */
class ImageResize extends BaseImageResize
{
    /**
     * RGB background array
     *
     * @var array
     */
    protected $background = ['r' => 255, 'g' => 255, 'b' => 255];

    protected $exactSize = [];

    public function setBackground(int $r, int $g, int $b): self
    {
        $this->background['r'] = $r;
        $this->background['g'] = $g;
        $this->background['b'] = $b;
        return $this;
    }

    /**
     * Saves new image
     * Overridden here to allow for a custom bg color
     *
     * @param string $filename
     * @param string $image_type
     * @param integer $quality
     * @param integer $permissions
     * @param boolean $exact_size
     * @return $this
     */
    public function save($filename, $image_type = null, $quality = null, $permissions = null, $exact_size = false)
    {
        $image_type = $image_type ?: $this->source_type;
        $quality = is_numeric($quality) ? (int)abs($quality) : null;
        if (!$exact_size && count($this->exactSize) == 2) {
            $exact_size = $this->exactSize;
        }

        switch ($image_type) {
            case IMAGETYPE_GIF:
                if (!empty($exact_size) && is_array($exact_size)) {
                    $dest_image = imagecreatetruecolor($exact_size[0], $exact_size[1]);
                } else {
                    $dest_image = imagecreatetruecolor($this->getDestWidth(), $this->getDestHeight());
                }

                $background = imagecolorallocatealpha(
                    $dest_image,
                    $this->background['r'],
                    $this->background['g'],
                    $this->background['b'],
                    1
                );
                imagecolortransparent($dest_image, $background);
                imagefill($dest_image, 0, 0, $background);
                imagesavealpha($dest_image, true);
                break;

            case IMAGETYPE_JPEG:
                if (!empty($exact_size) && is_array($exact_size)) {
                    $dest_image = imagecreatetruecolor($exact_size[0], $exact_size[1]);
                    $background = imagecolorallocate(
                        $dest_image,
                        $this->background['r'],
                        $this->background['g'],
                        $this->background['b']
                    );
                    imagefilledrectangle($dest_image, 0, 0, $exact_size[0], $exact_size[1], $background);
                } else {
                    $dest_image = imagecreatetruecolor($this->getDestWidth(), $this->getDestHeight());
                    $background = imagecolorallocate(
                        $dest_image,
                        $this->background['r'],
                        $this->background['g'],
                        $this->background['b']
                    );
                    imagefilledrectangle($dest_image, 0, 0, $this->getDestWidth(), $this->getDestHeight(), $background);
                }
                break;

            case IMAGETYPE_WEBP:
                if (version_compare(PHP_VERSION, '5.5.0', '<')) {
                    throw new ImageResizeException('For WebP support PHP >= 5.5.0 is required');
                }
                if (!empty($exact_size) && is_array($exact_size)) {
                    $dest_image = imagecreatetruecolor($exact_size[0], $exact_size[1]);
                    $background = imagecolorallocate(
                        $dest_image,
                        $this->background['r'],
                        $this->background['g'],
                        $this->background['b']
                    );
                    imagefilledrectangle($dest_image, 0, 0, $exact_size[0], $exact_size[1], $background);
                } else {
                    $dest_image = imagecreatetruecolor($this->getDestWidth(), $this->getDestHeight());
                    $background = imagecolorallocate(
                        $dest_image,
                        $this->background['r'],
                        $this->background['g'],
                        $this->background['b']
                    );
                    imagefilledrectangle($dest_image, 0, 0, $this->getDestWidth(), $this->getDestHeight(), $background);
                }
                break;

            case IMAGETYPE_PNG:
                if (!$this->quality_truecolor && !imageistruecolor($this->source_image)) {
                    if (!empty($exact_size) && is_array($exact_size)) {
                        $dest_image = imagecreate($exact_size[0], $exact_size[1]);
                    } else {
                        $dest_image = imagecreate($this->getDestWidth(), $this->getDestHeight());
                    }
                } else {
                    if (!empty($exact_size) && is_array($exact_size)) {
                        $dest_image = imagecreatetruecolor($exact_size[0], $exact_size[1]);
                    } else {
                        $dest_image = imagecreatetruecolor($this->getDestWidth(), $this->getDestHeight());
                    }
                }

                imagealphablending($dest_image, false);
                imagesavealpha($dest_image, true);

                $background = imagecolorallocatealpha(
                    $dest_image,
                    $this->background['r'],
                    $this->background['g'],
                    $this->background['b'],
                    0
                );
                //imagecolortransparent($dest_image, $background);
                imagefill($dest_image, 0, 0, $background);
                break;
        }

        imageinterlace($dest_image, $this->interlace);

        if ($this->gamma_correct) {
            imagegammacorrect($this->source_image, 2.2, 1.0);
        }

        if (!empty($exact_size) && is_array($exact_size)) {
            if ($this->getSourceHeight() < $this->getSourceWidth()) {
                $this->dest_x = 0;
                $this->dest_y = ($exact_size[1] - $this->getDestHeight()) / 2;
            }
            if ($this->getSourceHeight() > $this->getSourceWidth()) {
                $this->dest_x = ($exact_size[0] - $this->getDestWidth()) / 2;
                $this->dest_y = 0;
            }
        }

        imagecopyresampled(
            $dest_image,
            $this->source_image,
            $this->dest_x,
            $this->dest_y,
            $this->source_x,
            $this->source_y,
            $this->getDestWidth(),
            $this->getDestHeight(),
            $this->source_w,
            $this->source_h
        );

        if ($this->gamma_correct) {
            imagegammacorrect($dest_image, 1.0, 2.2);
        }


        $this->applyFilter($dest_image);

        switch ($image_type) {
            case IMAGETYPE_GIF:
                imagegif($dest_image, $filename);
                break;

            case IMAGETYPE_JPEG:
                if ($quality === null || $quality > 100) {
                    $quality = $this->quality_jpg;
                }

                imagejpeg($dest_image, $filename, $quality);
                break;

            case IMAGETYPE_WEBP:
                if (version_compare(PHP_VERSION, '5.5.0', '<')) {
                    throw new ImageResizeException('For WebP support PHP >= 5.5.0 is required');
                }
                if ($quality === null) {
                    $quality = $this->quality_webp;
                }

                imagewebp($dest_image, $filename, $quality);
                break;

            case IMAGETYPE_PNG:
                if ($quality === null || $quality > 9) {
                    $quality = $this->quality_png;
                }

                imagepng($dest_image, $filename, $quality);
                break;
        }

        if ($permissions) {
            chmod($filename, $permissions);
        }

        imagedestroy($dest_image);

        return $this;
    }

    public function resizeToFill(?int $width, ?int $height, bool $allowEnlarge = false): self
    {
        $this->resizeToBestFit($width, $height, $allowEnlarge);
        if ($width > $this->dest_w) {
            $this->dest_x = intval(round($width / 2 - $this->dest_w / 2));
        }
        if ($height > $this->dest_h) {
            $this->dest_y = intval(round($height / 2 - $this->dest_h / 2));
        }
        $this->exactSize = [$width, $height];

        return $this;
    }
}
