<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Tests\Adapter;

use Grommet\ImageResizer\Adapter\Local;
use Grommet\ImageResizer\Exception\InvalidStrategy;
use Grommet\ImageResizer\Exception\ResizeException;
use Grommet\ImageResizer\Strategy\Crop;
use Grommet\ImageResizer\Strategy\Exact;
use Grommet\ImageResizer\Strategy\Fill;
use Grommet\ImageResizer\Strategy\Fit;
use PHPUnit\Framework\TestCase;

/**
 * Local Resize Adapter Test Suite
 */
class LocalTest extends TestCase
{
    public function testResizeFit(): void
    {
        $adapter = new Local();
        $source = $this->resourceDir() . 'test.jpg';
        $destination = $this->resourceDir() . 'out.jpg';
        $adapter->resize($source, $destination, new Fit(100));
        $this->assertTrue(file_exists($destination));
        $size = getimagesize($destination);
        $this->assertSame(100, $size[0]);
        $this->assertSame(50, $size[1]);
        unlink($destination);
    }

    public function testResizeExact(): void
    {
        $adapter = new Local();
        $source = $this->resourceDir() . 'test.jpg';
        $destination = $this->resourceDir() . 'out.jpg';
        $adapter->resize($source, $destination, new Exact(100, 100));
        $this->assertTrue(file_exists($destination));
        $size = getimagesize($destination);
        $this->assertSame(100, $size[0]);
        $this->assertSame(100, $size[1]);
        unlink($destination);
    }

    public function testResizeCrop(): void
    {
        $adapter = new Local();
        $source = $this->resourceDir() . 'test.jpg';
        $destination = $this->resourceDir() . 'out.jpg';
        $adapter->resize($source, $destination, new Crop(100, 100));
        $this->assertTrue(file_exists($destination));
        $size = getimagesize($destination);
        $this->assertSame(100, $size[0]);
        $this->assertSame(100, $size[1]);
        unlink($destination);
    }

    public function testResizeInvalidSource(): void
    {
        $this->expectException(ResizeException::class);

        $adapter = new Local();
        $source = $this->resourceDir() . 'bogus.jpg';
        $destination = $this->resourceDir() . 'out.jpg';
        $adapter->resize($source, $destination, new Fill());
    }

    public function testResizeInvalidStrategy(): void
    {
        $this->expectException(InvalidStrategy::class);
        $this->expectExceptionMessage('Strategy not supported by this adapter');

        $adapter = new Local();
        $source = $this->resourceDir() . 'test.jpg';
        $destination = $this->resourceDir() . 'out.jpg';
        $adapter->resize($source, $destination, new Fill());
    }

    private function resourceDir(): string
    {
        return dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR;
    }
}
