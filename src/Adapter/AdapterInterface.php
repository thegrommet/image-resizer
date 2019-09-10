<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Adapter;

use Grommet\ImageResizer\Strategy\StrategyInterface;

/**
 * Adapter Interface
 */
interface AdapterInterface
{
    /**
     * Resize an image from source to destination
     *
     * @param string $source
     * @param string $destination
     * @param StrategyInterface $strategy
     * @return bool
     */
    public function resize(string $source, string $destination, StrategyInterface $strategy): bool;

    /**
     * Return resize strategies supported by this adapter
     *
     * @return array
     */
    public function supportedStrategies(): array;
}
