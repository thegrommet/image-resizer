<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Tests\Strategy;

use Grommet\ImageResizer\Strategy\Crop;
use PHPUnit\Framework\TestCase;

/**
 * Crop strategy test suite
 */
class CropTest extends TestCase
{
    public function testBindConfig(): void
    {
        $strategy = new Crop();
        $strategy->bindConfig([
            'width' => 100,
            'height' => 50,
            'cropMode' => 'c'
        ]);
        $this->assertSame(100, $strategy->width);
        $this->assertSame(50, $strategy->height);
        $this->assertSame('c', $strategy->cropMode);
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate(bool $expected, array $config): void
    {
        $strategy = new Crop();
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
                    'cropMode' => 'c'
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
                    'height' => 50,
                    'cropMode' => 'c'
                ]
            ],
            [
                false,
                []
            ]
        ];
    }
}
