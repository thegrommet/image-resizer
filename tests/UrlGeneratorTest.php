<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Tests;

use Grommet\ImageResizer\ImageResizer\UrlGenerator;
use PHPUnit\Framework\TestCase;

/**
 * URL Generator Test
 */
class UrlGeneratorTest extends TestCase
{
    public function testImageUrl(): void
    {
        $gen = new UrlGenerator();
        $params = [
            'strategy' => 'exact',
            'width' => 100,
            'height' => 80
        ];
        $this->assertSame('/exact_w-100_h-80/i/m/image.jpg', $gen->imageUrl('/i/m/image.jpg', $params));
        $params = [
            'strategy' => 'fit',
            'width' => 100,
            'height' => 80
        ];
        $this->assertSame('/fit_w-100_h-80/image.jpg', $gen->imageUrl('image.jpg', $params));

        $gen->baseUrl = '/media/';
        $this->assertSame('/media/fit_w-100_h-80/image.jpg', $gen->imageUrl('image.jpg', $params));

        $gen->baseUrl = 'http://test.com/media';
        $expected = sprintf('http://test.com/media/%s/image.jpg', urlencode('fit_w-100_h-80'));
        $this->assertSame($expected, $gen->imageUrl('image.jpg', $params));

        $params = [
            'w' => 100,
            'h' => 80
        ];
        $this->assertSame($expected, $gen->imageUrl('image.jpg', $params));

        $params = ['strategy' => 'optimize'];
        $this->assertSame('http://test.com/media/optimize/image.jpg', $gen->imageUrl('image.jpg', $params));
        $this->assertSame('http://test.com/media/optimize/image.jpg', $gen->imageUrl('image.jpg', []));
    }

    public function testImageUrlPresets(): void
    {
        $presets = [
            'small' => [
                'strategy' => 'fit',
                'width' => 400,
                'height' => 300
            ],
            'medium' => [
                'strategy' => 'crop',
                'width' => 500,
                'height' => 400
            ]
        ];
        $gen = new UrlGenerator('http://test.com/media', $presets);
        $gen->addPreset('large', []);
        $this->assertSame(
            'http://test.com/media/fit_w-400_h-300/image.jpg',
            $gen->imageUrl('image.jpg', ['size' => 'small'])
        );
        $this->assertSame(
            'http://test.com/media/crop_w-500_h-400_m-c/image.jpg',
            $gen->imageUrl('image.jpg', ['size' => 'medium'])
        );
        $this->assertSame(
            'http://test.com/media/crop_w-500_h-500_m-c/image.jpg',
            $gen->imageUrl('image.jpg', ['size' => 'medium', 'h' => 500])
        );
        $this->assertSame(
            'http://test.com/media/optimize/image.jpg',
            $gen->imageUrl('image.jpg', ['size' => 'large'])
        );
        $this->assertSame(
            'http://test.com/media/optimize/image.jpg',
            $gen->imageUrl('image.jpg', ['size' => 'bogus'])
        );
    }
}
