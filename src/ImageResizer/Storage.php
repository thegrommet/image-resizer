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
            throw new StorageException('Invalid source directory', StorageException::CODE_NOT_FOUND);
        } elseif (!is_dir($this->sourceBase)) {
            throw new StorageException('Cannot read source directory', StorageException::CODE_FORBIDDEN);
        }
        $this->destinationBase = rtrim($destinationBase, '/\\');
        if (empty($this->destinationBase)) {
            throw new StorageException('Invalid destination directory', StorageException::CODE_UNPROCESSABLE);
        } elseif (!is_dir($this->destinationBase) && !@mkdir($this->destinationBase, 0755, true)) {
            throw new StorageException('Cannot write to destination directory', StorageException::CODE_FORBIDDEN);
        }
    }

    public function sourcePath(string $filePath): string
    {
        if (strstr($filePath, '..') !== false) {
            throw new StorageException('Invalid file path', StorageException::CODE_FORBIDDEN);
        }
        return $this->sourceBase . DIRECTORY_SEPARATOR . $this->normalizePath($filePath);
    }

    public function destinationPath(string $filePath, bool $makeDir = false): string
    {
        if (strstr($filePath, '..') !== false) {
            throw new StorageException('Invalid file path', StorageException::CODE_FORBIDDEN);
        }
        $path = $this->destinationBase . DIRECTORY_SEPARATOR . $this->normalizePath($filePath);
        if ($makeDir && !is_dir(dirname($path))) {
            @mkdir(dirname($path), 0755, true);
        }
        return $path;
    }

    /**
     * Normalize path separators for the current OS and remove the leading path separator
     *
     * @param string $filePath
     * @return string
     */
    private function normalizePath(string $filePath): string
    {
        $filePath = ltrim($filePath, '/\\');
        if (DIRECTORY_SEPARATOR === '\\') {
            $replace = '/';
        } else {
            $replace = '\\';
        }
        return str_replace($replace, DIRECTORY_SEPARATOR, $filePath);
    }
}
