<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Tests;

/**
 * Loads images trait
 */
trait LoadsImages
{
    protected function resourceDir(string $sub = ''): string
    {
        $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR;
        if ($sub) {
            $dir .= $sub . DIRECTORY_SEPARATOR;
        }
        return $dir;
    }

    /**
     * Recursively remove a directory.
     *
     * @param string $dir
     * @return bool
     */
    protected function removeDirectory(string $dir): bool
    {
        if (stripos($dir, $this->resourceDir()) !== 0) {
            return false;
        }
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!$this->removeDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }
}
