<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Tests\Strategy;

use Grommet\ImageResizer\Strategy\Fill;
use PHPUnit\Framework\TestCase;

/**
 * Fill strategy test suite
 */
class FillTest extends TestCase
{
    public function testBindConfig(): void
    {
        $strategy = new Fill();
        $strategy->bindConfig([
            'width' => 100,
            'height' => 50,
            'background' => '#000'
        ]);
        $this->assertSame(100, $strategy->width);
        $this->assertSame(50, $strategy->height);
        $this->assertSame('#000', $strategy->background);
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate(bool $expected, array $config): void
    {
        $strategy = new Fill();
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
                    'background' => '#000'
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
