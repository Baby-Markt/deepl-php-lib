# DeepL PHP Library

Simple PHP Library for DeepL API.

## Installation

Use composer if you want to use this library in your project.

```bash
composer require babymarkt/deepl-php-lib
```

## Usage

```php
$authKey = 'AUTH KEY';
$deepl   = new DeepL($authKey);

$result = $deepl->translate('Hallo Welt', 'de', 'en');

print_r($result);
```

## Tests

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