<?php

declare(strict_types=1);

namespace Grommet\ImageResizer;

use Grommet\ImageResizer\Exception\ResizeException;

/**
 * Preset Resizer
 */
class PresetResizer
{
    /**
     * @var Resizer
     */
    private $resizer;

    /**
     * @var array
     */
    private $presets = [];

    public function __construct(
        string $sourceBasePath,
        string $destinationBasePath,
        array $presets = [],
        string $adapter = 'local',
        array $adapterConfig = []
    ) {
        foreach ($presets as $name => $params) {
            $this->addPreset($name, $params);
        }
        $this->resizer = new Resizer($sourceBasePath, $destinationBasePath, $adapter, $adapterConfig);
    }

    public function resize(string $sourceName, string $size): string
    {
        if (!isset($this->presets[$size])) {
            throw new ResizeException('Size not specified', ResizeException::CODE_UNPROCESSABLE);
        }
        $strategy = Strategy::createFromConfig($this->presets[$size]);
        $destination = $strategy . DIRECTORY_SEPARATOR . ltrim($sourceName, '\\/');
        return $this->resizer->resize($sourceName, $destination, ['strategy' => $strategy]);
    }

    public function addPreset(string $name, array $resizeParams): self
    {
        $this->presets[$name] = $resizeParams;
        return $this;
    }
}
