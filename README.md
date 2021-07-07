# deepl-php-lib

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Simple PHP Library for DeepL API. You can translate one or multiple text strings (up to 50) per request.

🇩🇪🇦🇹🇨🇭🇬🇧🇺🇸🇪🇸🇲🇽🇫🇷🇮🇹🇯🇵🇳🇱🇵🇱🇵🇹🇧🇷🇷🇺🇨🇳🇬🇷🇩🇰🇨🇿🇪🇪🇫🇮🇭🇺🇱🇹🇱🇻🇷🇴🇷🇸🇸🇰🇸🇪

[Official DeepL API][link-deepl]

[CHANGELOG](CHANGELOG.md)

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

Use the DeepL API Pro:
```php
$authKey = '<AUTH KEY>';
$deepl   = new DeepL($authKey,2,'api.deepl.com');
```

### Translate
Translate one Text:

```php
$translatedText = $deepl->translate('Hallo Welt', 'de', 'en');
echo $translatedText[0]['text'].PHP_EOL;
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
    echo $translation['text'].PHP_EOL;
}
```

| param               | Description                                                                                                                                                                                                                                                                                                                                                                                                               |
|---------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| $text               | Text to be translated. Only UTF8-encoded plain text is supported. The parameter may be specified as an Array and translations are returned in the same order as they are requested. Each of the array values may contain multiple sentences. Up to 50 texts can be sent for translation in one request.                                                                                                            |
| $sourceLang         | Language of the text to be translated. <br>default: de                                                                                                                                                                                                                                                                                                                                                                                   |
| $targetLang         | The language into which the text should be translated. <br> default: en                                                                                                                                                                                                                                                                                                                                                                    |
| $tagHandling        | Sets which kind of tags should be handled. Options currently available: "xml"                                                                                                                                                                                                                                                                                                                                             |
| $ignoreTags         | Array of XML tags that indicate text not to be translated. <br> default: null                                                                                                                                                                                                                                                                                                                                                  |
| $formality          | Sets whether the translated text should lean towards formal or informal language. This feature currently works for all target languages except "EN" (English), "EN-GB" (British English), "EN-US" (American English), "ES" (Spanish), "JA" (Japanese) and "ZH" (Chinese).<br><br>Possible options are:<br>"default" (default)<br>"more" - for a more formal language<br>"less" - for a more informal language |
| $splitSentences     | Array of XML tags which always cause splits  <br> default: null                                                                                                                                                                                                                                                                                                                                                                |
| $preserveFormatting | Sets whether the translation engine should respect the original formatting, even if it would usually correct some aspects. Possible values are:<br>"0" (default)<br>"1"<br>The formatting aspects affected by this setting include:<br>Punctuation at the beginning and end of the sentence<br>Upper/lower case at the beginning of the sentence                                                                          |
| $nonSplittingTags   | Comma-separated list of XML tags which never split sentences.   <br> default: null                                                                                                                                                                                                                                                                                                                                                            |
| $outlineDetection   | See: https://www.deepl.com/docs-api/handling-xml/outline-detection/ <br> default: 1                                                                                                                                                                                                                                                                                                                                                     |
| $splittingTags      | Array of XML tags which always cause splits. <br> default: null                                                                                                                                                                                                                                                                                                                                                              |

### Supported languages
In Version 2 we removed the internal List of supported Languages.
Instead, you can now get an array with the supported Languages directly form DeepL:

```php
$languagesArray = $deepl->languages();

foreach ($languagesArray as $language) {
    echo 'Name: '.$language['name'].' Api-Shorthand: '.$language['language'].PHP_EOL;
}
```
You can check for the supported Source-Languages:
```php
$sourceLanguagesArray = $deepl->languages('source');

foreach ($sourceLanguagesArray as $srouceLanguage) {
    echo 'Name: '.$srouceLanguage['name'].' Api-shorthand: '.$srouceLanguage['language'].PHP_EOL;
}
```

Check for the supported Target-Languages:
```php
$targetLanguagesArray = $deepl->languages('target');

foreach ($targetLanguagesArray as $targetLanguage) {
    echo 'Name: '.$targetLanguage['name'].' Api-Shorthand: '.$targetLanguage['language'].PHP_EOL;
}
```
### Monitoring usage
You can now check how much you translate, as well as the limit:
```php
$usageArray = $deepl->usage();

echo 'You have used '.$usageArray['character_count'].' of '.$usageArray['character_limit'].' in the current billing period.'.PHP_EOL;
 
```

### Configuring cURL requests
If you need to use a proxy, you can configure the underlying curl client to use one. You can also specify a timeout to avoid waiting for several minutes if Deepl is unreachable
```php
$deepl->setTimeout(10); //give up after 10 seconds
$deepl->setProxy('http://corporate-proxy.com:3128');
$deepl->setProxyCredentials('username:password');

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
[link-travis]: https://travis-ci.com/Baby-Markt/deepl-php-lib
[link-scrutinizer]: https://scrutinizer-ci.com/g/Baby-Markt/deepl-php-lib/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/Baby-Markt/deepl-php-lib
[link-downloads]: https://packagist.org/packages/babymarkt/deepl-php-lib
[link-author]: https://github.com/Baby-Markt
[link-contributors]: ../../contributors
[link-deepl]: https://www.deepl.com/docs-api/introduction/
