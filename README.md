# Grommet Image Resizer

A PHP library for resizing images

## Usage

```php
$resizer = new \Grommet\ImageResizer\Resizer();
$resizer->resize($source, $destination, ['strategy' => 'fit', 'width' => 100]);
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
