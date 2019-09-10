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
    use LoadsImages;

    public function testResize(): void
    {
        $destinationBase = $this->resourceDir('new');
        $urlResizer = new UrlResizer(
            $this->resourceDir(),
            $destinationBase,
            'https://cdn.site.com/media/resized'
        );

        $url = 'https://cdn.site.com/media/resized/fit_w-100/t/test.jpg';
        $destination = implode(
            DIRECTORY_SEPARATOR,
            [rtrim($destinationBase, DIRECTORY_SEPARATOR), 'fit_w-100', 't', 'test.jpg']
        );

        $res = $urlResizer->resize($url);
        $this->assertSame($destination, $res);
        $this->assertTrue(file_exists($destination));
        $size = getimagesize($destination);
        $this->assertSame(100, $size[0]);
        $this->assertSame(50, $size[1]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->removeDirectory($this->resourceDir('new'));
    }
}
