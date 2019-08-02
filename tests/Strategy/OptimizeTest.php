<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Tests\Strategy;

use Grommet\ImageResizer\Strategy\Optimize;
use PHPUnit\Framework\TestCase;

/**
 * Optimize strategy test suite
 */
class OptimizeTest extends TestCase
{
    /**
     * @dataProvider validateProvider
     */
    public function testValidate(bool $expected, array $config): void
    {
        $strategy = new Optimize();
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
                true,
                [
                    'q' => 80
                ]
            ],
            [
                true,
                []
            ]
        ];
    }

    public function testToString(): void
    {
        $stategy = new Optimize(null, null, 80);
        $this->assertSame('optimize_q=80', $stategy->__toString());
        $stategy = new Optimize();
        $this->assertSame('optimize', $stategy->__toString());
    }
}
