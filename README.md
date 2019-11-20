# deepl-php-lib

[![Build Status](https://travis-ci.org/Baby-Markt/deepl-php-lib.svg?branch=master)](https://travis-ci.org/Baby-Markt/deepl-php-lib)
[![Latest Stable Version](https://poser.pugx.org/babymarkt/deepl-php-lib/v/stable.svg)](https://packagist.org/packages/babymarkt/deepl-php-lib)
[![Total Downloads](https://poser.pugx.org/babymarkt/deepl-php-lib/downloads.png)](https://packagist.org/packages/babymarkt/deepl-php-lib)

Simple PHP Library for DeepL API. You can translate one or multiple text strings (up to 50) per request.

## Installation

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

## Run PHPUnit Tests

Clone the repository.

```bash
git clone git@github.com:Baby-Markt/deepl-php-lib.git
```

Create phpunit.xml from dist file.

```bash
cp phpunit.xml.dist phpunit.xml
```

Install composer dependencies.

```bash
composer install
```

Now, run PHPUnit Tests.

```bash
vendor/bin/phpunit
```

Or use composer.

```bash
composer test
```
