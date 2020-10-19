<?php

declare(strict_types=1);

namespace Grommet\ImageResizer;

use Grommet\ImageResizer\ImageResizer\UrlParser;

/**
 * URL Resizer
 */
class UrlResizer
{
    /**
     * @var Resizer
     */
    private $resizer;

    /**
     * @var UrlParser
     */
    private $parser;

    public function __construct(
        string $sourceBasePath,
        string $destinationBasePath,
        string $baseUrl = '',
        string $adapter = 'local',
        array $adapterConfig = []
    ) {
        $this->resizer = new Resizer($sourceBasePath, $destinationBasePath, $adapter, $adapterConfig);
        $this->parser = new UrlParser($baseUrl);
    }

    public function resize(string $url): string
    {
        $this->parser->parse($url);
        $destination = $this->parser->strategy . DIRECTORY_SEPARATOR . ltrim($this->parser->imagePath, '\\/');
        return $this->resizer->resize($this->parser->imagePath, $destination, ['strategy' => $this->parser->strategy]);
    }
}
