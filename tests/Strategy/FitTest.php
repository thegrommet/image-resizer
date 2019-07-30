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
}
