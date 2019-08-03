<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\ImageResizer;

use Grommet\ImageResizer\Exception\InvalidUrl;
use Grommet\ImageResizer\Strategy;
use Grommet\ImageResizer\Strategy\AbstractStrategy;

/**
 * Image URL parser
 */
class UrlParser
{
    /**
     * Base URL to where resized images are stored
     *
     * @var string
     */
    public $baseUrl = '';

    /**
     * @var string
     */
    public $imagePath;

    /**
     * @var AbstractStrategy
     */
    public $strategy;

    public function __construct(string $baseUrl = '')
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Parse an image URL into a strategy and image path
     *
     * @param string $url
     * @return bool
     */
    public function parse(string $url): bool
    {
        if (strpos($url, rtrim($this->baseUrl, '/') . '/') === 0) {
            $url = substr($url, strlen($this->baseUrl));
        }
        $url = trim($url, '/');
        if (strpos($url, '/') === false) {
            throw new InvalidUrl('Missing resize parameters', InvalidUrl::CODE_NOT_FOUND);
        }
        list($params, $imagePath) = explode('/', $url, 2);
        $imagePath = trim($imagePath, '/');
        if (empty($imagePath)) {
            throw new InvalidUrl('Invalid image path', InvalidUrl::CODE_NOT_FOUND);
        }
        $this->imagePath = '/' . $imagePath;
        if (strpos($params, '_') === false) {
            $strategy = $params;
        } else {
            list($strategy, $params) = explode('_', $params, 2);
        }
        if (empty($strategy) || preg_match('/[^a-z0-9]/i', $strategy) !== 0) {
            throw new InvalidUrl('Invalid resize strategy', InvalidUrl::CODE_NOT_FOUND);
        }
        $params = explode(',', $params);
        $properties = [];
        foreach ($params as $param) {
            if (strpos($url, '=') !== false) {
                list($key, $val) = explode('=', $param);
                $properties[$key] = $val;
            }
        }
        $this->strategy = Strategy::factory($strategy, $properties);
        if (!$this->strategy->validate()) {
            throw new InvalidUrl('Required parameters not set on strategy', InvalidUrl::CODE_UNPROCESSABLE);
        }
        return true;
    }
}
