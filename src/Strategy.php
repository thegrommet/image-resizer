<?php
declare(strict_types=1);

namespace Grommet\ImageResizer;

use Grommet\ImageResizer\Exception\InvalidArgument;
use Grommet\ImageResizer\Exception\InvalidStrategy;
use Grommet\ImageResizer\Strategy\Fit;
use Grommet\ImageResizer\Strategy\Optimize;
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
        throw new InvalidStrategy('Invalid strategy', InvalidArgument::CODE_UNPROCESSABLE);
    }

    public static function createFromConfig(array $resizeParams): StrategyInterface
    {
        if (isset($resizeParams['strategy'])) {
            if (is_string($resizeParams['strategy'])) {
                return Strategy::factory($resizeParams['strategy'], $resizeParams);
            } elseif ($resizeParams['strategy'] instanceof StrategyInterface) {
                return $resizeParams['strategy'];
            } else {
                throw new InvalidStrategy('Invalid resize strategy', InvalidStrategy::CODE_UNPROCESSABLE);
            }
        } else {  // guess strategy
            if (isset($resizeParams['width']) || isset($resizeParams['height'])
                || isset($resizeParams['w']) || isset($resizeParams['h'])) {
                $strategy = new Fit();
            } else {
                $strategy = new Optimize();
            }
            $strategy->bindConfig($resizeParams);
            return $strategy;
        }
    }
}
