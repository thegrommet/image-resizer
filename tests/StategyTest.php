<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Tests;

use Grommet\ImageResizer\Exception\InvalidArgument;
use Grommet\ImageResizer\Strategy;
use Grommet\ImageResizer\Strategy\Crop;
use Grommet\ImageResizer\Strategy\Exact;
use Grommet\ImageResizer\Strategy\Fill;
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
        $this->expectException(InvalidArgument::class);
        Strategy::factory('bogus');
    }
}
