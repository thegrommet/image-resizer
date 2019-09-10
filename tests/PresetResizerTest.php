<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Tests;

use Grommet\ImageResizer\PresetResizer;
use PHPUnit\Framework\TestCase;

/**
 * Preset Resizer Test
 */
class PresetResizerTest extends TestCase
{
    use LoadsImages;

    public function testResize(): void
    {
        $destinationBase = $this->resourceDir('new');
        $presets = [
            'small' => [
                'strategy' => 'fit',
                'width' => 100,
                'height' => 50
            ],
            'medium' => [
                'strategy' => 'crop',
                'width' => 500,
                'height' => 400
            ]
        ];
        $resizer = new PresetResizer(
            $this->resourceDir(),
            $destinationBase,
            $presets
        );

        $destination = implode(
            DIRECTORY_SEPARATOR,
            [rtrim($destinationBase, DIRECTORY_SEPARATOR), 'fit_w-100_h-50', 't', 'test.jpg']
        );

        $res = $resizer->resize('t/test.jpg', 'small');
        $this->assertSame($destination, $res);
        $this->assertTrue(file_exists($destination));
        $size = getimagesize($destination);
        $this->assertSame(100, $size[0]);
        $this->assertSame(50, $size[1]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->removeDirectory($this->resourceDir('new'));
    }
}
