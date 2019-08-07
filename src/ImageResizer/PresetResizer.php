<?php
declare(strict_types=1);

namespace Grommet\ImageResizer;

use Grommet\ImageResizer\Exception\ResizeException;

/**
 * Preset Resizer
 */
class PresetResizer
{
    private $sourceBasePath;
    private $destinationBasePath;
    private $adapter;

    /**
     * @var array
     */
    private $presets = [];

    public function __construct(
        string $sourceBasePath,
        string $destinationBasePath,
        array $presets = [],
        string $adapter = 'local'
    ) {
        $this->sourceBasePath = $sourceBasePath;
        $this->destinationBasePath = $destinationBasePath;
        foreach ($presets as $name => $params) {
            $this->addPreset($name, $params);
        }
        $this->adapter = $adapter;
    }

    public function resize(string $sourceName, string $size): string
    {
        if (!isset($this->presets[$size])) {
            throw new ResizeException('Size not specified', ResizeException::CODE_UNPROCESSABLE);
        }
        $strategy = Strategy::createFromConfig($this->presets[$size]);
        $destination = rtrim($this->destinationBasePath, '\\/') . DIRECTORY_SEPARATOR . $strategy;
        $resizer = new Resizer($this->sourceBasePath, $destination, $this->adapter);
        return $resizer->resize($sourceName, null, ['strategy' => $strategy]);
    }

    public function addPreset(string $name, array $resizeParams): self
    {
        $this->presets[$name] = $resizeParams;
        return $this;
    }
}
