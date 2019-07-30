<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Strategy;

/**
 * Strategy Interface
 */
interface StrategyInterface
{
    /**
     * Set config properties on this object
     *
     * @param array $config
     */
    public function bindConfig(array $config): void;

    /**
     * Validate that necessary params are set on the strategy
     *
     * @return bool
     */
    public function validate(): bool;
}
