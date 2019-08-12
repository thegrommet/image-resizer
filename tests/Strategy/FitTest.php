<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Tests\Strategy;

use Grommet\ImageResizer\Strategy\Fit;
use PHPUnit\Framework\TestCase;

/**
 * Fit strategy test suite
 */
class FitTest extends TestCase
{
    /**
     * @dataProvider validateProvider
     */
    public function testValidate(bool $expected, array $config): void
    {
        $strategy = new Fit();
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
                    'w' => 100
                ]
            ],
            [
                true,
                [
                    'height' => 50
                ]
            ],
            [
                false,
                []
            ]
        ];
    }

    public function testToString(): void
    {
        $stategy = new Fit(100, 50, 80);
        $this->assertSame('fit_w-100_h-50_q-80', $stategy->__toString());
        $stategy = new Fit(100);
        $this->assertSame('fit_w-100', $stategy->__toString());
        $stategy = new Fit(null, 50);
        $this->assertSame('fit_h-50', $stategy->__toString());
    }
}
