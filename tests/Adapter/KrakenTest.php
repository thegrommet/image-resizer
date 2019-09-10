<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Tests\Adapter;

use Grommet\ImageResizer\Adapter\Kraken;
use Grommet\ImageResizer\Strategy\Crop;
use Grommet\ImageResizer\Strategy\Fill;
use Grommet\ImageResizer\Strategy\Fit;
use Grommet\ImageResizer\Strategy\Optimize;
use Grommet\ImageResizer\Tests\LoadsImages;
use PHPUnit\Framework\TestCase;

/**
 * Kraken Resize Adapter Test Suite
 */
class KrakenTest extends TestCase
{
    use LoadsImages;

    /**
     * This will actually hit the API
     */
    public function _testResizeFit(): void
    {
        $adapter = new Kraken('key', 'secret');
        $source = $this->resourceDir() . 'test.jpg';
        $destination = $this->resourceDir() . 'out.jpg';
        $adapter->resize($source, $destination, new Fit(100, 50));
        $this->assertTrue(file_exists($destination));
        $size = getimagesize($destination);
        $this->assertSame(100, $size[0]);
        $this->assertSame(50, $size[1]);
        unlink($destination);
    }

    public function testStrategyToConfig(): void
    {
        $class = new \ReflectionClass(Kraken::class);
        $method = $class->getMethod('strategyToConfig');
        $method->setAccessible(true);
        $adapter = new Kraken('key', 'secret');

        $strategy = new Optimize();
        $this->assertSame([
            'quality' => 80
        ], $method->invokeArgs($adapter, [$strategy]));

        $strategy = new Fit(100, 50, 85);
        $this->assertSame([
            'quality' => 85,
            'resize' => [
                'strategy' => 'fit',
                'width' => 100,
                'height' => 50
            ]
        ], $method->invokeArgs($adapter, [$strategy]));

        $strategy = new Fill(100, 50, null, '#fff');
        $this->assertSame([
            'quality' => 80,
            'resize' => [
                'strategy' => 'fill',
                'width' => 100,
                'height' => 50,
                'background' => '#fff'
            ]
        ], $method->invokeArgs($adapter, [$strategy]));

        $strategy = new Crop(100, 50, null, 'c');
        $this->assertSame([
            'quality' => 80,
            'resize' => [
                'strategy' => 'crop',
                'width' => 100,
                'height' => 50,
                'crop_mode' => 'c'
            ]
        ], $method->invokeArgs($adapter, [$strategy]));
    }
}
