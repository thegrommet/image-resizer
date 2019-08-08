<?php
declare(strict_types=1);

namespace Grommet\ImageResizer;

use Grommet\ImageResizer\Adapter\AdapterInterface;
use Grommet\ImageResizer\Exception\InvalidArgument;
use Grommet\ImageResizer\Exception\ResizeException;
use Grommet\ImageResizer\ImageResizer\Storage;
use Grommet\ImageResizer\Strategy\StrategyInterface;

/**
 * Image Resizer
 */
class Resizer
{
    /**
     * @var StrategyInterface
     */
    private $strategy;

    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var Storage
     */
    private $storage;

    public function __construct(string $sourceBase, string $destinationBase, string $adapter = 'local')
    {
        $this->storage = new Storage($sourceBase, $destinationBase);
        $this->adapter = Adapter::factory($adapter);
    }

    /**
     * Resize the image in source and return the path of the new image
     *
     * @param string $sourceName
     * @param string|null $destinationName
     * @param array $config
     * @return string
     */
    public function resize(string $sourceName, ?string $destinationName = null, array $config = []): string
    {
        if (empty($sourceName)) {
            throw new InvalidArgument('Invalid source file', InvalidArgument::CODE_NOT_FOUND);
        }
        if (!$destinationName) {
            $destinationName = $sourceName;
        }
        if (empty($destinationName)) {
            throw new InvalidArgument('Invalid destination file', InvalidArgument::CODE_UNPROCESSABLE);
        }
        if ($this->storage->sourcePath($sourceName) != $this->storage->destinationPath($destinationName)
            && file_exists($this->storage->destinationPath($destinationName))) {
            // already exists, no sense in re-generating
            return $this->storage->destinationPath($destinationName);
        }
        if (isset($config['strategy'])) {
            if (is_string($config['strategy'])) {
                $strategy = Strategy::factory($config['strategy'], $config);
                $this->setStrategy($strategy);
            } elseif ($config['strategy'] instanceof StrategyInterface) {
                $this->setStrategy($config['strategy']);
            }
        }
        if (isset($config['adapter'])) {
            if (is_string($config['adapter'])) {
                $adapter = Adapter::factory($config['adapter']);
                $this->setAdapter($adapter);
            } elseif ($config['adapter'] instanceof AdapterInterface) {
                $this->setAdapter($config['adapter']);
            }
        }

        if (!$this->adapter) {
            throw new InvalidArgument('Image adapter not set', InvalidArgument::CODE_UNPROCESSABLE);
        }
        if (!$this->strategy) {
            throw new InvalidArgument('Resize strategy not set', InvalidArgument::CODE_UNPROCESSABLE);
        }
        if (!$this->strategy->validate()) {
            throw new InvalidArgument('Required parameters not set on strategy', InvalidArgument::CODE_UNPROCESSABLE);
        }

        if ($this->adapter->resize(
            $this->storage->sourcePath($sourceName),
            $this->storage->destinationPath($destinationName, true),
            $this->strategy
        )) {
            return $this->storage->destinationPath($destinationName);
        }
        throw new ResizeException('Unable to resize the image', InvalidArgument::CODE_INTERNAL_ERROR);
    }

    public function setAdapter(AdapterInterface $adapter): self
    {
        $this->adapter = $adapter;
        return $this;
    }

    public function setStrategy(StrategyInterface $strategy): self
    {
        $this->strategy = $strategy;
        return $this;
    }

    public function setStorage(Storage $storage): self
    {
        $this->storage = $storage;
        return $this;
    }
}
