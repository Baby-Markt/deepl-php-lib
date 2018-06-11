# DeepL PHP Library

Simple PHP Library for DeepL API.

## Installation

```bash
composer require babymarkt/deepl
```

## Usage

```php
$authKey = 'AUTH KEY';
$deepl   = new DeepL($authKey);

$result = $deepl->translate('Hallo Welt', 'de', 'en');

print_r($result);
```

## Tests

First, create phpunit.xml from dist file.

```bash
cp phpunit.xml.dist phpunit.xml
```

Now, run PHPUnit Tests.

```
php phpunit-4.8.phar
```