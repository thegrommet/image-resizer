<?php
declare(strict_types=1);

namespace Grommet\ImageResizer\Tests;

use Grommet\ImageResizer\Exception\InvalidUrl;
use Grommet\ImageResizer\ImageResizer\UrlParser;
use Grommet\ImageResizer\Strategy\Fit;
use Grommet\ImageResizer\Strategy\Optimize;
use PHPUnit\Framework\TestCase;

/**
 * URL Parser Test
 */
class UrlParserTest extends TestCase
{
    public function testParse(): void
    {
        $parser = new UrlParser();
        $this->assertTrue($parser->parse('/fit_w=400,h=300,q=85/i/m/image.jpg'));
        $this->assertSame('/i/m/image.jpg', $parser->imagePath);
        $this->assertInstanceOf(Fit::class, $parser->strategy);
        $this->assertSame(400, $parser->strategy->width);
        $this->assertSame(300, $parser->strategy->height);
        $this->assertSame(85, $parser->strategy->quality);

        $this->assertTrue($parser->parse('/optimize/image.jpg'));
        $this->assertSame('/image.jpg', $parser->imagePath);
        $this->assertInstanceOf(Optimize::class, $parser->strategy);
    }

    public function testParseWithBaseUri(): void
    {
        $parser = new UrlParser();
        $parser->baseUrl = '/media/products';
        $this->assertTrue($parser->parse('/media/products/fit_w=400,h=300,q=85/i/m/image.jpg'));
        $this->assertSame('/i/m/image.jpg', $parser->imagePath);
        $this->assertInstanceOf(Fit::class, $parser->strategy);
        $this->assertSame(400, $parser->strategy->width);
        $this->assertSame(300, $parser->strategy->height);
        $this->assertSame(85, $parser->strategy->quality);
    }

    public function testParseWithBaseUrl(): void
    {
        $parser = new UrlParser();
        $parser->baseUrl = 'https://www.test.com/media/products/';
        $this->assertTrue($parser->parse('https://www.test.com/media/products/fit_w=400,h=300/i/m/image.jpg'));
        $this->assertSame('/i/m/image.jpg', $parser->imagePath);
        $this->assertInstanceOf(Fit::class, $parser->strategy);
        $this->assertSame(400, $parser->strategy->width);
        $this->assertSame(300, $parser->strategy->height);
    }

    /**
     * @dataProvider parseInvalidProvider
     */
    public function testParseInvalid($url): void
    {
        $this->expectException(InvalidUrl::class);
        $parser = new UrlParser();
        $parser->parse($url);
    }

    public function parseInvalidProvider(): array
    {
        return [
            ['/'],
            ['/w=400,h=300,q=85/i/m/image.jpg'],
            ['/crop/i/m/image.jpg'],
            ['/crop_/i/m/image.jpg'],
            ['/crop_w=400,h=300,q=85/']
        ];
    }
}
