# Grommet Image Resizer

A PHP library for resizing images

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
// $url = 'https://cdn.site.com/media/fit_w=100/i/image.jpg'
```

#### Resize presets
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
$urlGen = new \Grommet\ImageResizer\UrlGenerator('https://cdn.site.com/media', $presets);
$url = $urlGen->imageUrl('i/image.jpg', ['size' => 'small']);
// $url = 'https://cdn.site.com/media/fit_w=293,h=219/i/image.jpg'
```

### Resizing service
```php
$urlResizer = new \Grommet\ImageResizer\UrlResizer(
    '/path/to/images',
    '/path/to/save',
    'https://cdn.site.com/media/resized'
);

// incoming request for a resized image
$url = 'https://cdn.site.com/media/resized/fit_w=100/i/image.jpg';
$newPath = $urlResizer->resize($url);
// $newPath = '/path/to/save/fit_w=100/i/image.jpg'

header('content-type', 'image/jpeg');
echo file_get_contents($newPath);
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
