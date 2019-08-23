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

    public function testDestinationPathMakeDir(): void
    {
        $store = new Storage($this->resourceDir(), $this->resourceDir('new'));
        $destDir = $this->resourceDir('new' . DIRECTORY_SEPARATOR . 'i');
        $this->assertSame($destDir . 'image.jpg', $store->destinationPath('i/image.jpg', true));
        $this->assertTrue(is_dir($destDir));
        rmdir($destDir);
        rmdir(dirname($destDir));
    }

    public function testDestinationPathInvalid(): void
    {
        $this->expectException(StorageException::class);
        $this->expectExceptionCode(StorageException::CODE_FORBIDDEN);
        $store = new Storage($this->resourceDir(), $this->resourceDir('new'));
        $store->destinationPath('/../image.jpg');
    }

    public function testPathNormalization(): void
    {
        $base = $this->resourceDir();
        $store = new Storage($base, $base);
        $expected = $base . 't' . DIRECTORY_SEPARATOR . 'test.jpg';
        if (DIRECTORY_SEPARATOR === '/') {
            $this->assertSame($expected, $store->sourcePath('t\\test.jpg'));
            $this->assertSame($expected, $store->destinationPath('t\\test.jpg'));
        } else {
            $this->assertSame($expected, $store->sourcePath('t/test.jpg'));
            $this->assertSame($expected, $store->destinationPath('t/test.jpg'));
        }
    }

    /**
     * @dataProvider constructInvalidProvider
     */
    public function testConstructInvalid($source, $destination): void
    {
        $this->expectException(StorageException::class);
        $this->expectExceptionCode(StorageException::CODE_FORBIDDEN);
        new Storage($source, $destination);
    }

    public function constructInvalidProvider(): array
    {
        return [
            ['/bogus', '/bogus'],
            // below two don't fail on CI
            //[$this->resourceDir(), '/bogus'],
            //['/bogus', $this->resourceDir()],
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
