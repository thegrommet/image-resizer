<?php
declare(strict_types=1);

namespace Grommet\ImageResizer;

use Grommet\ImageResizer\ImageResizer\UrlParser;

/**
 * URL Resizer
 */
class UrlResizer
{
    private $sourceBasePath;
    private $destinationBasePath;
    private $baseUrl;
    private $adapter;

    public function __construct(
        string $sourceBasePath,
        string $destinationBasePath,
        string $baseUrl = '',
        string $adapter = 'local'
    ) {
        $this->sourceBasePath = $sourceBasePath;
        $this->destinationBasePath = $destinationBasePath;
        $this->baseUrl = $baseUrl;
        $this->adapter = $adapter;
    }

    public function resize(string $url): string
    {
        $parser = new UrlParser($this->baseUrl);
        $parser->parse($url);

        $resizer = new Resizer($this->sourceBasePath, $this->destinationBasePath, $this->adapter);
        $resizer->setStrategy($parser->strategy);
        return $resizer->resize($parser->imagePath);
    }
}