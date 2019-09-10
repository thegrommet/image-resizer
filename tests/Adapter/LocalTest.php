<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Tests\Adapter;

use Grommet\ImageResizer\Adapter\Gumlet\ImageResize;
use Grommet\ImageResizer\Adapter\Local;
use Grommet\ImageResizer\Exception\ResizeException;
use Grommet\ImageResizer\Strategy\Crop;
use Grommet\ImageResizer\Strategy\Exact;
use Grommet\ImageResizer\Strategy\Fill;
use Grommet\ImageResizer\Strategy\Fit;
use Grommet\ImageResizer\Strategy\Optimize;
use Grommet\ImageResizer\Tests\LoadsImages;
use PHPUnit\Framework\TestCase;

/**
 * Local Resize Adapter Test Suite
 */
class LocalTest extends TestCase
{
    use LoadsImages;

    public function testResizeOptimize(): void
    {
        $adapter = new Local();
        $source = $this->resourceDir() . 'test.jpg';
        $destination = $this->resourceDir() . 'out.jpg';
        $adapter->resize($source, $destination, new Optimize());
        $this->assertTrue(file_exists($destination));
        $sourceSize = getimagesize($destination);
        $destSize = getimagesize($destination);
        $this->assertEquals($sourceSize, $destSize);
        unlink($destination);
    }

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

    public function testResizeFillWidth(): void
    {
        $source = $this->resourceDir() . 'fill-high.png';
        $destination = $this->resourceDir() . 'fill-out.png';
        $resizer = new ImageResize($source);
        $resizer->interlace = 0;
        $adapter = new Local();
        $adapter->setResizer($resizer);
        $adapter->resize($source, $destination, new Fill(100, 100, null, '#ff0000'));
        $this->assertTrue(file_exists($destination));
        $size = getimagesize($destination);
        $this->assertSame(100, $size[0]);
        $this->assertSame(100, $size[1]);
        $res = imagecreatefrompng($destination);
        $red = 16711680;
        $this->assertSame($red, imagecolorat($res, 0, 0));
        $this->assertSame(0, imagecolorat($res, 25, 50));
        $this->assertSame($red, imagecolorat($res, 76, 99));
        imagedestroy($res);
        unlink($destination);
    }

    public function testResizeFillHeight(): void
    {
        $source = $this->resourceDir() . 'fill-wide.png';
        $destination = $this->resourceDir() . 'fill-out.png';
        $resizer = new ImageResize($source);
        $resizer->interlace = 0;
        $adapter = new Local();
        $adapter->setResizer($resizer);
        $adapter->resize($source, $destination, new Fill(100, 100, null, '#ff0000'));
        $this->assertTrue(file_exists($destination));
        $size = getimagesize($destination);
        $this->assertSame(100, $size[0]);
        $this->assertSame(100, $size[1]);
        $res = imagecreatefrompng($destination);
        $red = 16711680;
        $this->assertSame($red, imagecolorat($res, 0, 0));
        $this->assertSame(0, imagecolorat($res, 0, 50));
        $this->assertSame($red, imagecolorat($res, 0, 76));
        imagedestroy($res);
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
        $this->expectExceptionCode(ResizeException::CODE_NOT_FOUND);

        $adapter = new Local();
        $source = $this->resourceDir() . 'bogus.jpg';
        $destination = $this->resourceDir() . 'out.jpg';
        $adapter->resize($source, $destination, new Fill());
    }

    /**
     * @dataProvider normalizeJpgQualityProvider
     */
    public function testNormalizeJpgQuality(int $expected, int $input): void
    {
        $class = new \ReflectionClass(Local::class);
        $method = $class->getMethod('normalizeJpgQuality');
        $method->setAccessible(true);
        $adapter = new Local();
        $adapter->defaultJpgQuality = 85;
        $this->assertSame($expected, $method->invokeArgs($adapter, [$input]));
    }

    public function normalizeJpgQualityProvider(): array
    {
        return [
            [85, 85],
            [85, 0],
            [85, 101]
        ];
    }

    /**
     * @dataProvider normalizePngQualityProvider
     */
    public function testNormalizePngQuality(int $expected, int $input): void
    {
        $class = new \ReflectionClass(Local::class);
        $method = $class->getMethod('normalizePngQuality');
        $method->setAccessible(true);
        $adapter = new Local();
        $adapter->defaultPngQuality = 6;
        $this->assertSame($expected, $method->invokeArgs($adapter, [$input]));
    }

    public function normalizePngQualityProvider(): array
    {
        return [
            [9, 85],
            [8, 84],
            [6, 0],
            [6, 101]
        ];
    }

    /**
     * @dataProvider hexToRgbProvider
     */
    public function testHexToRgb(array $expected, string $input): void
    {
        $class = new \ReflectionClass(Local::class);
        $method = $class->getMethod('hexToRgb');
        $method->setAccessible(true);
        $adapter = new Local();
        $this->assertEquals($expected, $method->invokeArgs($adapter, [$input]));
    }

    public function hexToRgbProvider(): array
    {
        return [
            [
                [255, 255, 255],
                '#ffffff'
            ],
            [
                [255, 255, 255],
                '#fff'
            ],
            [
                [0, 0, 0],
                '#000'
            ],
            [
                [95, 158, 160],
                '#5f9ea0'
            ],
        ];
    }
}
