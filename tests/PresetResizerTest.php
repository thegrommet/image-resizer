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
    public function testResize(): void
    {
        $destinationBase = $this->resourceDir('new');
        $presets = [
            'small' => [
                'strategy' => 'fit',
                'width' => 400,
                'height' => 200
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
            [rtrim($destinationBase, DIRECTORY_SEPARATOR), 'fit_w=400,h=200', 't', 'test.jpg']
        );

        $res = $resizer->resize('t/test.jpg', 'small');
        $this->assertSame($destination, $res);
        $this->assertTrue(file_exists($destination));
        $size = getimagesize($destination);
        $this->assertSame(400, $size[0]);
        $this->assertSame(200, $size[1]);
        unlink($destination);
        rmdir(dirname($destination));
        rmdir(dirname($destination, 2));
        rmdir(dirname($destination, 3));
    }

    private function resourceDir(string $sub = ''): string
    {
        $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR;
        if ($sub) {
            $dir .= $sub . DIRECTORY_SEPARATOR;
        }
        return $dir;
    }
}
