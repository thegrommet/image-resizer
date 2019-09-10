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
    use LoadsImages;

    public function testResize(): void
    {
        $destination = $this->resourceDir() . 'out.jpg';
        $resizer = new Resizer($this->resourceDir(), $this->resourceDir());
        $res = $resizer->resize('test.jpg', 'out.jpg', ['strategy' => 'fit', 'width' => 100]);
        $this->assertSame($destination, $res);
        $this->assertTrue(file_exists($destination));
        $size = getimagesize($destination);
        $this->assertSame(100, $size[0]);
        $this->assertSame(50, $size[1]);
        unlink($destination);
    }

    public function testResizeSameName(): void
    {
        $destination = $this->resourceDir() . 'out.jpg';
        copy($this->resourceDir() . 'test.jpg', $destination);
        $resizer = new Resizer($this->resourceDir(), $this->resourceDir());
        $res = $resizer->resize('out.jpg', null, ['strategy' => 'fit', 'width' => 100]);
        $this->assertSame($destination, $res);
        $this->assertTrue(file_exists($destination));
        $size = getimagesize($destination);
        $this->assertSame(100, $size[0]);
        $this->assertSame(50, $size[1]);
        unlink($destination);
    }

    public function testResizeNewDir(): void
    {
        $destination = $this->resourceDir('new') . 'out.jpg';
        $resizer = new Resizer($this->resourceDir(), $this->resourceDir('new'));
        $res = $resizer->resize('test.jpg', 'out.jpg', ['strategy' => 'fit', 'width' => 100]);
        $this->assertSame($destination, $res);
        $this->assertTrue(is_dir(dirname($destination)));
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
