<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Tests;

use Grommet\ImageResizer\Adapter;
use Grommet\ImageResizer\Adapter\Kraken;
use Grommet\ImageResizer\Adapter\Local;
use Grommet\ImageResizer\Exception\InvalidArgument;
use PHPUnit\Framework\TestCase;

/**
 * Adapter Test
 */
class AdapterTest extends TestCase
{
    public function testLocalFactory(): void
    {
        $this->assertInstanceOf(Local::class, Adapter::factory('local'));
        $this->assertInstanceOf(Local::class, Adapter::factory('Local'));
    }
    
    public function testFactoryInvalid(): void
    {
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionCode(InvalidArgument::CODE_UNPROCESSABLE);
        Adapter::factory('bogus');
    }

    public function testKrakenFactory(): void
    {
        $this->assertInstanceOf(Kraken::class, Adapter::factory('kraken', ['key', 'secret']));
    }
}
