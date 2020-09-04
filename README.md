# deepl-php-lib

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Simple PHP Library for DeepL API. You can translate one or multiple text strings (up to 50) per request.

🇬🇧🇩🇪🇫🇷🇪🇸🇵🇹🇮🇹🇷🇺🇯🇵🇨🇳🇵🇱🇳🇱🇸🇪🇩🇰🇫🇮🇬🇷🇨🇿🇷🇴🇭🇺🇸🇰🇧🇬🇸🇮🇱🇹🇱🇻🇪🇪🇲🇹

## Install

Use composer if you want to use this library in your project.

```bash
composer require babymarkt/deepl-php-lib
```

## Usage

Create an instance with your auth key:

```php
$authKey = '<AUTH KEY>';
$deepl   = new DeepL($authKey);
```

Translate one Text:

```php
$translatedText = $deepl->translate('Hallo Welt', 'de', 'en');
echo $translatedText;
```

Translate multiple Texts:

```php
$text = array(
    'Hallo Welt',
    'Wie geht es dir',
    'Macht doch einfach mal'
);

$translations = $deepl->translate($text, 'de', 'en');

foreach ($translations as $translation) {
    echo $translation['text'];
}
```
### Supported languages
In Version 2 we removed the internal List of supported Languages.
Instead, you can now get an array with the supported Languages directly form DeepL:

```php
$languagesArray = $deepl->languages();

foreach ($languagesArray as $language) {
    echo $language['name'];
    echo $language['language'];
}
```

### Monitoring usage
You can now check ow much you translate, as well as the limits set.
```php
$usageArray = $deepl->usage();

echo 'You have used '.$usageArray['character_count'].' of '.$usageArray['character_limit'].' in in the current billing period.';
 
```
## Testing

Run PHP_CodeSniffer Tests:

```bash
composer cs
```

Run PHPMD Tests:

```bash
composer md
```

Run PHPUnit Tests:

```bash
composer test
```

Run all tests:

```bash
composer test:all
```

## Credits

- [babymarkt.de GmbH][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/babymarkt/deepl-php-lib.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Baby-Markt/deepl-php-lib/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/Baby-Markt/deepl-php-lib.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Baby-Markt/deepl-php-lib.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/babymarkt/deepl-php-lib.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/babymarkt/deepl-php-lib
[link-travis]: https://travis-ci.org/Baby-Markt/deepl-php-lib
[link-scrutinizer]: https://scrutinizer-ci.com/g/Baby-Markt/deepl-php-lib/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/Baby-Markt/deepl-php-lib
[link-downloads]: https://packagist.org/packages/babymarkt/deepl-php-lib
[link-author]: https://github.com/Baby-Markt
[link-contributors]: ../../contributors
