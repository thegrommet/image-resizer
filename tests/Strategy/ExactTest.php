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
    public function testContruct(): void
    {
        $strategy = new Exact(100, 50);
        $this->assertSame(100, $strategy->width);
        $this->assertSame(50, $strategy->height);
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
    }

    public function testBindConfigAliases(): void
    {
        $strategy = new Exact();
        $strategy->bindConfig([
            'w' => 100,
            'height' => 50,
            'bogus' => 'not set'
        ]);
        $this->assertSame(100, $strategy->width);
        $this->assertSame(50, $strategy->height);
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
                    'height' => 50
                ]
            ],
            [
                true,
                [
                    'w' => 100,
                    'height' => 50
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
}
