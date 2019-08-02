<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Tests;

use Grommet\ImageResizer\Resizer;
use PHPUnit\Framework\TestCase;

/**
 * Resizer Test
 */
class ResizerTest extends TestCase
{
    public function testResize(): void
    {
        $source = $this->resourceDir() . 'test.jpg';
        $destination = $this->resourceDir() . 'out.jpg';
        $resizer = new Resizer();
        $resizer->resize($source, $destination, ['strategy' => 'fit', 'width' => 100]);
        $this->assertTrue(file_exists($destination));
        $size = getimagesize($destination);
        $this->assertSame(100, $size[0]);
        $this->assertSame(50, $size[1]);
        unlink($destination);
    }

    public function testResizeNewDir(): void
    {
        $source = $this->resourceDir() . 'test.jpg';
        $destination = $this->resourceDir() . 'new/out.jpg';
        $resizer = new Resizer();
        $resizer->resize($source, $destination, ['strategy' => 'fit', 'width' => 100]);
        $this->assertTrue(is_dir(dirname($destination)));
        $this->assertTrue(file_exists($destination));
        $size = getimagesize($destination);
        $this->assertSame(100, $size[0]);
        $this->assertSame(50, $size[1]);
        unlink($destination);
        rmdir(dirname($destination));
    }

    private function resourceDir(): string
    {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR;
    }
}
