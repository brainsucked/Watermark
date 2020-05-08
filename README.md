# Apply watermark image

Apply watermark image with PHP

## Getting Started

Include watermark class
```php
require_once __DIR__ . '/../vendor/watermark/watermark.php';
```

Watermark class started
```php
$watermark = new \ImageCopyright\Watermark();
```

Apply watermark
```php
$watermark->apply('from.jpg', 'to.jpg', 'path/to/watermark.png', 3);
```

Parameters of apply
1: From image, original image
2: Target image, image destination
3: Watermark image
4: Watermark position number:
- 0: Centered
- 1: Top Left
- 2: Top Right
- 3: Footer Right
- 4: Footer left
- 5: Top Centered
- 6: Center Right
- 7: Footer Centered
- 8: Center Left

## Credits

Josemar Davi Luedke <josemarluedke@gmail.com>

## License

This project is licensed under the MIT License