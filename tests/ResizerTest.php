<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Tests;

use Grommet\ImageResizer\Adapter\Local;
use Grommet\ImageResizer\Exception\InvalidArgument;
use Grommet\ImageResizer\Resizer;
use Grommet\ImageResizer\Strategy\Crop;
use Grommet\ImageResizer\Strategy\Exact;
use Grommet\ImageResizer\Strategy\Fill;
use PHPUnit\Framework\TestCase;

/**
 * Resizer Test
 */
class ResizerTest extends TestCase
{
    public function testStrategyFactory(): void
    {
        $this->assertInstanceOf(Exact::class, Resizer::strategyFactory('exact'));
        $this->assertInstanceOf(Exact::class, Resizer::strategyFactory('EXACT'));
        $this->assertInstanceOf(Fill::class, Resizer::strategyFactory('fill'));
        $this->assertInstanceOf(Crop::class, Resizer::strategyFactory('Crop'));
    }
    public function testStrategyFactoryInvalid(): void
    {
        $this->expectException(InvalidArgument::class);
        Resizer::strategyFactory('bogus');
    }
    
    public function testAdapterFactory(): void
    {
        $this->assertInstanceOf(Local::class, Resizer::adapterFactory('local'));
        $this->assertInstanceOf(Local::class, Resizer::adapterFactory('Local'));
    }
    public function testAdapterFactoryInvalid(): void
    {
        $this->expectException(InvalidArgument::class);
        Resizer::adapterFactory('bogus');
    }

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
