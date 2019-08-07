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
        $expected = sprintf('/%s/i/m/image.jpg', urlencode('exact_w=100,h=80'));
        $this->assertSame($expected, $gen->imageUrl('/i/m/image.jpg', $params));
        $params = [
            'strategy' => 'fit',
            'width' => 100,
            'height' => 80
        ];
        $expected = sprintf('/%s/image.jpg', urlencode('fit_w=100,h=80'));
        $this->assertSame($expected, $gen->imageUrl('image.jpg', $params));

        $gen->baseUrl = '/media/';
        $expected = sprintf('/media/%s/image.jpg', urlencode('fit_w=100,h=80'));
        $this->assertSame($expected, $gen->imageUrl('image.jpg', $params));

        $gen->baseUrl = 'http://test.com/media';
        $expected = sprintf('http://test.com/media/%s/image.jpg', urlencode('fit_w=100,h=80'));
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
        $expected = sprintf('http://test.com/media/%s/image.jpg', urlencode('fit_w=400,h=300'));
        $this->assertSame($expected, $gen->imageUrl('image.jpg', ['size' => 'small']));
        $expected = sprintf('http://test.com/media/%s/image.jpg', urlencode('crop_w=500,h=400,m=c'));
        $this->assertSame($expected, $gen->imageUrl('image.jpg', ['size' => 'medium']));
        $expected = sprintf('http://test.com/media/%s/image.jpg', urlencode('crop_w=500,h=500,m=c'));
        $this->assertSame($expected, $gen->imageUrl('image.jpg', ['size' => 'medium', 'h' => 500]));
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
