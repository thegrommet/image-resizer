<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\ImageResizer;

use Grommet\ImageResizer\Exception\StorageException;

/**
 * Image file system storage
 */
class Storage
{
    private $sourceBase = '';
    private $destinationBase = '';

    public function __construct(string $sourceBase, string $destinationBase)
    {
        $this->sourceBase = rtrim($sourceBase, '/\\');
        if (empty($this->sourceBase)) {
            throw new StorageException('Invalid source directory');
        } elseif (!is_dir($this->sourceBase)) {
            throw new StorageException('Cannot read source directory');
        }
        $this->destinationBase = rtrim($destinationBase, '/\\');
        if (empty($this->destinationBase)) {
            throw new StorageException('Invalid destination directory');
        } elseif (!is_dir($this->destinationBase) && !@mkdir($this->destinationBase, 0755, true)) {
            throw new StorageException('Cannot write to destination directory');
        }
    }

    public function sourcePath(string $filePath): string
    {
        if (strstr($filePath, '..') !== false) {
            throw new StorageException('Invalid file path');
        }
        return $this->sourceBase . DIRECTORY_SEPARATOR . ltrim($filePath, '/\\');
    }

    public function destinationPath(string $filePath, bool $makeDir = false): string
    {
        if (strstr($filePath, '..') !== false) {
            throw new StorageException('Invalid file path');
        }
        $path = $this->destinationBase . DIRECTORY_SEPARATOR . ltrim($filePath, '/\\');
        if ($makeDir && !is_dir(dirname($path)) && !@mkdir(dirname($path), 0755, true)) {
            throw new StorageException('Cannot write to destination directory');
        }
        return $path;
    }
}
