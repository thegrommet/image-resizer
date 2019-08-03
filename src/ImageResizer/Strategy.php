<?php
declare(strict_types=1);

namespace Grommet\ImageResizer;

use Grommet\ImageResizer\Exception\InvalidArgument;
use Grommet\ImageResizer\Strategy\StrategyInterface;

/**
 * Strategy factory class
 */
class Strategy
{
    public static function factory(string $strategy, array $config = []): StrategyInterface
    {
        $class = __NAMESPACE__ . '\\Strategy\\' . ucfirst(strtolower($strategy));
        if (@class_exists($class)) {
            $strategy = new $class();
            if ($strategy instanceof StrategyInterface) {
                $strategy->bindConfig($config);
                return $strategy;
            }
        }
        throw new InvalidArgument('Invalid strategy', InvalidArgument::CODE_UNPROCESSABLE);
    }
}
