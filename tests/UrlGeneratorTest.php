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
        $this->assertSame('/exact_w=100,h=80/i/m/image.jpg', $gen->imageUrl('/i/m/image.jpg', $params));
        $params = [
            'strategy' => 'fit',
            'width' => 100,
            'height' => 80
        ];
        $this->assertSame('/fit_w=100,h=80/image.jpg', $gen->imageUrl('image.jpg', $params));

        $gen->baseUrl = '/media/';
        $this->assertSame('/media/fit_w=100,h=80/image.jpg', $gen->imageUrl('image.jpg', $params));

        $gen->baseUrl = 'http://test.com/media';
        $this->assertSame('http://test.com/media/fit_w=100,h=80/image.jpg', $gen->imageUrl('image.jpg', $params));
    }
}
