<?php
declare(strict_types=1);

namespace Grommet\ImageResizer;

use Grommet\ImageResizer\Adapter\AdapterInterface;
use Grommet\ImageResizer\Exception\InvalidArgument;
use Grommet\ImageResizer\Exception\ResizeException;
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
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $destination;

    public function __construct(string $adapter = 'local')
    {
        $adapter = Adapter::factory($adapter);
        $this->setAdapter($adapter);
    }

    public function resize(string $source = null, string $destination = null, array $config = []): bool
    {
        if ($source) {
            $this->source = $source;
        }
        if ($destination) {
            $this->destination = $destination;
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
            throw new InvalidArgument('Image adapter not set');
        }
        if (!$this->strategy) {
            throw new InvalidArgument('Resize strategy not set');
        }
        if (!$this->strategy->validate()) {
            throw new InvalidArgument('Required parameters not set on strategy');
        }

        $destDir = dirname($destination);
        if (!is_dir($destDir)) {
            if (!@mkdir($destDir, 0755, true)) {
                throw new ResizeException('Unable to create destination directory');
            }
        }

        return $this->adapter->resize($this->source, $this->destination, $this->strategy);
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

    public function setSourcePath(string $source): self
    {
        $this->source = $source;
        return $this;
    }

    public function setDestinationPath(string $destination): self
    {
        $this->destination = $destination;
        return $this;
    }
}
