<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Tests;

use Grommet\ImageResizer\Exception\InvalidStrategy;
use Grommet\ImageResizer\Strategy;
use Grommet\ImageResizer\Strategy\Crop;
use Grommet\ImageResizer\Strategy\Exact;
use Grommet\ImageResizer\Strategy\Fill;
use Grommet\ImageResizer\Strategy\Fit;
use Grommet\ImageResizer\Strategy\Optimize;
use PHPUnit\Framework\TestCase;

/**
 * Strategy Test
 */
class StrategyTest extends TestCase
{
    public function testFactory(): void
    {
        $this->assertInstanceOf(Exact::class, Strategy::factory('exact'));
        $this->assertInstanceOf(Exact::class, Strategy::factory('EXACT'));
        $this->assertInstanceOf(Fill::class, Strategy::factory('fill'));
        $this->assertInstanceOf(Crop::class, Strategy::factory('Crop'));
    }
    
    public function testFactoryInvalid(): void
    {
        $this->expectException(InvalidStrategy::class);
        $this->expectExceptionCode(InvalidStrategy::CODE_UNPROCESSABLE);
        Strategy::factory('bogus');
    }

    public function testCreateFromConfig(): void
    {
        $strategy = Strategy::createFromConfig(['strategy' => 'exact', 'w' => 100, 'h' => 50]);
        $this->assertInstanceOf(Exact::class, Strategy::factory('exact'));
        $this->assertSame(100, $strategy->width);
        $this->assertSame(50, $strategy->height);

        $this->assertInstanceOf(Exact::class, Strategy::createFromConfig(['strategy' => $strategy]));
        $this->assertInstanceOf(Optimize::class, Strategy::createFromConfig([]));

        $strategy = Strategy::createFromConfig(['w' => 100]);
        $this->assertInstanceOf(Fit::class, $strategy);
        $this->assertSame(100, $strategy->width);

        $strategy = Strategy::createFromConfig(['height' => 100]);
        $this->assertInstanceOf(Fit::class, $strategy);
        $this->assertSame(100, $strategy->height);
    }

    public function testCreateFromConfigInvalid(): void
    {
        $this->expectException(InvalidStrategy::class);
        $this->expectExceptionCode(InvalidStrategy::CODE_UNPROCESSABLE);
        Strategy::createFromConfig(['strategy' => 'bogus']);
    }
}
