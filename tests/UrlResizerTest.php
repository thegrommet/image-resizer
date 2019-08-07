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
        $destinationBase = $this->resourceDir('new');
        $urlResizer = new UrlResizer(
            $this->resourceDir(),
            $destinationBase,
            'https://cdn.site.com/media/resized'
        );

        $url = 'https://cdn.site.com/media/resized/fit_w%3D100/t/test.jpg';
        $destination = implode(
            DIRECTORY_SEPARATOR,
            [rtrim($destinationBase, DIRECTORY_SEPARATOR), 'fit_w=100', 't', 'test.jpg']
        );

        $res = $urlResizer->resize($url);
        $this->assertSame($destination, $res);
        $this->assertTrue(file_exists($destination));
        $size = getimagesize($destination);
        $this->assertSame(100, $size[0]);
        $this->assertSame(50, $size[1]);
        unlink($destination);
        rmdir(dirname($destination));
        rmdir(dirname($destination, 2));
        rmdir(dirname($destination, 3));
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
