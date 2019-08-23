# Grommet Image Resizer

A PHP library for resizing images

### Build status

[![CircleCI](https://circleci.com/gh/thegrommet/image-resizer.svg?style=svg)](https://circleci.com/gh/thegrommet/image-resizer)

## Usage

### Resizing
```php
$resizer = new \Grommet\ImageResizer\Resizer('/path/to/images', '/path/to/save');
$newPath = $resizer->resize('in.jpg', 'out.jpg', ['strategy' => 'fit', 'width' => 100]);
// $newPath = '/path/to/save/out.jpg'
```

### Generating URLs to assets
```php
$urlGen = new \Grommet\ImageResizer\UrlGenerator('https://cdn.site.com/media');
$url = $urlGen->imageUrl('i/image.jpg', ['strategy' => 'fit', 'width' => 100]);
// $url = 'https://cdn.site.com/media/fit_w-100/i/image.jpg'
```

### Resize presets
```php
$presets = [
    'small' => [
        'width' => 293,
        'height' => 219
    ],
    'large' => [
        'strategy' = 'crop',
        'width' => 500,
        'height' => 500
    ]
];

/* urls */
$urlGen = new \Grommet\ImageResizer\UrlGenerator('https://cdn.site.com/media', $presets);
$url = $urlGen->imageUrl('i/image.jpg', ['size' => 'small']);
// $url = 'https://cdn.site.com/media/fit_w-293_h-219/i/image.jpg'

/* files */
$resizer = new \Grommet\ImageResizer\PresetResizer(
    '/path/to/images',
    '/path/to/save',
    $presets
);
$newPath = $resizer->resize('image.jpg', 'large');
// $newPath = '/path/to/save/crop_w-500_h-500_m-c/image.jpg'
```

### Resizing service
```php
$urlResizer = new \Grommet\ImageResizer\UrlResizer(
    '/path/to/images',
    '/path/to/save',
    'https://cdn.site.com/media'
);

// incoming request for a resized image
$url = 'https://cdn.site.com/media/fit_w-100/i/image.jpg';
$newPath = $urlResizer->resize($url);
// $newPath = '/path/to/save/fit_w-100/i/image.jpg'

header('content-type', 'image/jpeg');
echo file_get_contents($newPath);
```

## Resize Adapters
By default, the resizer will use PHP's GD2 functions to resize the images. You may specify a different image resize 
adapter to and offload that work to a 3rd party.

### Kraken.io
Use Kraken's image resizing service once you have an account and API access:
```php
$resizer = new \Grommet\ImageResizer\Resizer(
    '/path/to/images',
    '/path/to/save',
    'kraken',
    ['api-key', 'api-secret']
);
$newPath = $resizer->resize('in.jpg', 'out.jpg', ['strategy' => 'fit', 'w' => 100, 'h' => 50]);
// $newPath = '/path/to/save/out.jpg'
```

## Running tests

```shell
composer test
```

## Code style & fix

```shell
# sniff src folder
composer fmt-check

# fix src folder
composer fmt
```
