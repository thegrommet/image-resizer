<?php
declare(strict_types=1);

namespace Grommet\ImageResizer;

use Grommet\ImageResizer\Exception\InvalidArgument;
use Grommet\ImageResizer\Adapter\AdapterInterface;

/**
 * Adapter factory class
 */
class Adapter
{
    public static function factory(string $type): AdapterInterface
    {
        $class = __NAMESPACE__ . '\\Adapter\\' . ucfirst(strtolower($type));
        if (@class_exists($class)) {
            $adapter = new $class();
            if ($adapter instanceof AdapterInterface) {
                return $adapter;
            }
        }
        throw new InvalidArgument('Invalid adapter', InvalidArgument::CODE_UNPROCESSABLE);
    }
}
