<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Tests\Strategy;

use Grommet\ImageResizer\Strategy\Exact;
use PHPUnit\Framework\TestCase;

/**
 * Exact strategy test suite
 */
class ExactTest extends TestCase
{
    public function testConstruct(): void
    {
        $strategy = new Exact(100, 50, 80);
        $this->assertSame(100, $strategy->width);
        $this->assertSame(50, $strategy->height);
        $this->assertSame(80, $strategy->quality);
    }

    public function testBindConfig(): void
    {
        $strategy = new Exact();
        $strategy->bindConfig([
            'width' => 100,
            'height' => 50,
            'bogus' => 'not set'
        ]);
        $this->assertSame(100, $strategy->width);
        $this->assertSame(50, $strategy->height);

        $strategy->bindConfig([
            'width' => '100',
            'height' => '50'
        ]);
        $this->assertSame(100, $strategy->width);
        $this->assertSame(50, $strategy->height);
    }

    public function testBindConfigAliases(): void
    {
        $strategy = new Exact();
        $strategy->bindConfig([
            'w' => 100,
            'height' => 50,
            'q' => 80,
            'bogus' => 'not set'
        ]);
        $this->assertSame(100, $strategy->width);
        $this->assertSame(50, $strategy->height);
        $this->assertSame(80, $strategy->quality);
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate(bool $expected, array $config): void
    {
        $strategy = new Exact();
        $strategy->bindConfig($config);
        $this->assertSame($expected, $strategy->validate());
    }

    public function validateProvider(): array
    {
        return [
            [
                true,
                [
                    'width' => 100,
                    'height' => 50,
                    'quality' => 85
                ]
            ],
            [
                true,
                [
                    'w' => 100,
                    'height' => 50,
                    'q' => 85
                ]
            ],
            [
                false,
                [
                    'w' => 100
                ]
            ],
            [
                false,
                [
                    'height' => 50
                ]
            ],
            [
                false,
                [
                    'W' => 50,
                    'Height' => 50
                ]
            ],
            [
                false,
                []
            ]
        ];
    }

    public function testToArray(): void
    {
        $strategy = new Exact(100, 50, 80);
        $this->assertEquals(['w' => 100, 'h' => 50, 'q' => 80], $strategy->toArray());
        $strategy = new Exact(100);
        $this->assertEquals(['w' => 100, 'h' => null, 'q' => null], $strategy->toArray());
    }

    public function testToString(): void
    {
        $stategy = new Exact(100, 50, 80);
        $this->assertSame('exact_w-100_h-50_q-80', $stategy->__toString());
        $stategy = new Exact(100, 50);
        $this->assertSame('exact_w-100_h-50', $stategy->__toString());
    }

    public function testName(): void
    {
        $strategy = new Exact();
        $this->assertSame('exact', $strategy->name());
    }
}
