<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Tests;

use Grommet\ImageResizer\UrlResizer;
use PHPUnit\Framework\TestCase;

/**
 * URL Resizer Test
 */
class UrlResizerTest extends TestCase
{
    public function testResize(): void
    {
        $destination = $this->resourceDir('new') . 'test.jpg';
        $urlResizer = new UrlResizer(
            $this->resourceDir(),
            $this->resourceDir('new'),
            'https://cdn.site.com/media/resized'
        );

        $url = 'https://cdn.site.com/media/resized/fit_w=100/test.jpg';

        $res = $urlResizer->resize($url);
        $this->assertSame($destination, $res);
        $this->assertTrue(file_exists($destination));
        $size = getimagesize($destination);
        $this->assertSame(100, $size[0]);
        $this->assertSame(50, $size[1]);
        unlink($destination);
        rmdir(dirname($destination));
    }

    private function resourceDir(string $sub = ''): string
    {
        $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR;
        if ($sub) {
            $dir .= $sub . DIRECTORY_SEPARATOR;
        }
        return $dir;
    }
}
