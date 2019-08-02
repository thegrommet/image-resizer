<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Tests;

use Grommet\ImageResizer\Exception\StorageException;
use Grommet\ImageResizer\ImageResizer\Storage;
use PHPUnit\Framework\TestCase;

/**
 * Storage Test
 */
class StorageTest extends TestCase
{
    public function testSourcePath(): void
    {
        $store = new Storage($this->resourceDir(), $this->resourceDir('new'));
        $this->assertSame($this->resourceDir() . 'image.jpg', $store->sourcePath('image.jpg'));
    }

    public function testSourcePathInvalid(): void
    {
        $this->expectException(StorageException::class);
        $store = new Storage($this->resourceDir(), $this->resourceDir('new'));
        $store->sourcePath('../image.jpg');
    }

    public function testDestinationPath(): void
    {
        $store = new Storage($this->resourceDir(), $this->resourceDir('new'));
        $this->assertSame($this->resourceDir('new') . 'image.jpg', $store->destinationPath('image.jpg'));
    }

    public function testDestinationPathInvalid(): void
    {
        $this->expectException(StorageException::class);
        $store = new Storage($this->resourceDir(), $this->resourceDir('new'));
        $store->destinationPath('/../image.jpg');
    }

    /**
     * @dataProvider constructInvalidProvider
     */
    public function testConstructInvalid($source, $destination): void
    {
        $this->expectException(StorageException::class);
        new Storage($source, $destination);
    }

    public function constructInvalidProvider(): array
    {
        return [
            ['/bogus', '/bogus'],
            [$this->resourceDir(), '/bogus'],
            ['/bogus', $this->resourceDir()],
        ];
    }

    private function resourceDir(string $sub = ''): string
    {
        $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR;
        if ($sub) {
            $dir .= $sub . DIRECTORY_SEPARATOR;
        }
        return $dir;
    }

    public function tearDown(): void
    {
        parent::tearDown();
        @rmdir($this->resourceDir('new'));
    }
}
