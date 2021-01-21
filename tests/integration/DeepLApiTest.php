<?php

namespace BabyMarkt\DeepL\integration;

use BabyMarkt\DeepL\DeepL;
use BabyMarkt\DeepL\DeepLException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Class DeepLTest
 *
 * @package BabyMarkt\DeepL
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class DeepLApiTest extends TestCase
{
    /**
     * DeepL Auth Key.
     *
     * @var bool|string
     */
    protected static array|bool|string $authKey = false;

    /**
     * Setup DeepL Auth Key.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $authKey = getenv('DEEPL_AUTH_KEY');

        if ($authKey === false) {
            return;
        }

        self::$authKey = $authKey;
    }

    /**
     * Get protected method
     *
     * @param $className
     * @param $methodName
     *
     * @throws ReflectionException
     *
     * @return ReflectionMethod
     */
    protected static function getMethod($className, $methodName)
    {
        $class = new ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * Test translate() success with v2 API
     * @throws DeepLException
     */
    public function testTranslateSuccess()
    {
        if (self::$authKey === false) {
            $this->markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl = new DeepL(self::$authKey);

        $germanText     = 'Hallo Welt';
        $expectedText   = 'Hello World';

        $translatedText = $deepl->translate($germanText);

        $this->assertEquals($expectedText, $translatedText[0]['text']);
    }

    /**
     * Test translate() success with v1 API
     * @throws DeepLException
     */
    public function testTranslateV1Success()
    {
        if (self::$authKey === false) {
            $this->markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl = new DeepL(self::$authKey, 1);

        $germanText     = 'Hallo Welt';
        $expectedText   = 'Hello World';

        $translatedText = $deepl->translate($germanText);

        $this->assertEquals($expectedText, $translatedText[0]['text']);
    }

    /**
     * Test translate() with unknown API-Version
     */
    public function testTranslateWrongVersion()
    {
        if (self::$authKey === false) {
            $this->markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }
        $germanText = 'Hallo Welt';
        $deepl      = new DeepL(self::$authKey, 3);

        $this->expectException('\BabyMarkt\DeepL\DeepLException');

        $deepl->translate($germanText);
    }

    /**
     * Test translate() with tag handling success
     */
    public function testTranslateTagHandlingSuccess()
    {
        if (self::$authKey === false) {
            $this->markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl = new DeepL(self::$authKey);

        $englishText  = '<strong>text to translate</strong>';
        $expectedText = '<strong>zu übersetzender Text</strong>';

        $translatedText = $deepl->translate(
            $englishText,
            'en',
            'de',
            'xml'
        );

        $this->assertEquals($expectedText, $translatedText[0]['text']);
    }

    /**
     * Test translate() with tag ignored success
     */
    public function testTranslateIgnoreTagsSuccess()
    {
        if (self::$authKey === false) {
            $this->markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl = new DeepL(self::$authKey);

        $englishText  = '<strong>text to do not translate</strong><p>text to translate</p>';
        $expectedText = '<strong>text to do not translate</strong><p>zu übersetzender Text</p>';

        $translatedText = $deepl->translate(
            $englishText,
            'en',
            'de',
            'xml',
            array('strong')
        );

        $this->assertEquals($expectedText, $translatedText[0]['text']);
    }

    /**
     * Test usage() has certain array-keys
     */
    public function testUsage()
    {
        if (self::$authKey === false) {
            $this->markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl    = new DeepL(self::$authKey);
        $response = $deepl->usage();

        $this->assertArrayHasKey('character_count', $response);
        $this->assertArrayHasKey('character_limit', $response);
    }

    /**
     * Test languages() has certain array-keys
     */
    public function testLanguages()
    {
        if (self::$authKey === false) {
            $this->markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl    = new DeepL(self::$authKey);
        $response = $deepl->languages();

        foreach ($response as $language) {
            $this->assertArrayHasKey('language', $language);
            $this->assertArrayHasKey('name', $language);
        }
    }

    /**
     * Test languages() can return the source-languages
     */
    public function testLanguagesSource()
    {
        if (self::$authKey === false) {
            $this->markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl    = new DeepL(self::$authKey);
        $response = $deepl->languages('source');

        foreach ($response as $language) {
            $this->assertArrayHasKey('language', $language);
            $this->assertArrayHasKey('name', $language);
        }
    }

    /**
     * Test languages()  can return the targe-languages
     */
    public function testLanguagesTarget()
    {
        if (self::$authKey === false) {
            $this->markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl    = new DeepL(self::$authKey);
        $response = $deepl->languages('target');

        foreach ($response as $language) {
            $this->assertArrayHasKey('language', $language);
            $this->assertArrayHasKey('name', $language);
        }
    }

    /**
     * Test languages() will fail with wrong Parameter
     */
    public function testLanguagesFail()
    {
        if (self::$authKey === false) {
            $this->markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl    = new DeepL(self::$authKey);

        $this->expectException('\BabyMarkt\DeepL\DeepLException');

        $deepl->languages('fail');
    }

    /**
     * Test translate() with all Params
     * @throws DeepLException
     */
    public function testTranslateWithAllParams()
    {
        if (self::$authKey === false) {
            $this->markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl = new DeepL(self::$authKey);

        $englishText  = '<strong>text to do not translate</strong><p>please translate this text</p>';
        $expectedText = '<strong>text to do not translate</strong><p>bitte übersetzen Sie diesen Text</p>';

        $translatedText = $deepl->translate(
            $englishText,
            'en',           //$sourceLanguage
            'de',        //$destinationLanguage
            'xml',             //$tagHandling
            array('html','html5','html6'), //$ignoreTags
            'more',                         //$formality
            'nonewlines',                  //$splitSentences
            1,                             //$preserveFormatting
            array('href','href2'),         //$nonSplittingTags
            0,                             //$outlineDetection
            array('p','br')                //$splittingTags
        );

        $this->assertEquals($expectedText, $translatedText[0]['text']);
    }

    /**
     * Test translate() $formality
     * @throws DeepLException
     */
    public function testTranslateFormality()
    {
        if (self::$authKey === false) {
            $this->markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl = new DeepL(self::$authKey);

        $englishText    = '<strong>text to do not translate</strong><p>please translate this text</p>';
        $expectedText   = '<stark>nicht zu übersetzender Text</stark><p>bitte diesen Text übersetzen</p>';
        $translatedText = $deepl->translate(
            $englishText,
            'en',           //$sourceLanguage
            'de',        //$destinationLanguage
            null,             //$tagHandling
            null, //$ignoreTags
            'less'                         //$formality
        );

        $this->assertEquals($expectedText, $translatedText[0]['text']);
    }

    /**
     * Test translate() $formality
     */
    public function testTranslateFormalityFail()
    {
        if (self::$authKey === false) {
            $this->markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl        = new DeepL(self::$authKey);
        $englishText  = '<strong>text to do not translate</strong><p>please translate this text</p>';

        $this->expectException('\BabyMarkt\DeepL\DeepLException');

        $deepl->translate(
            $englishText,
            'en',           //$sourceLanguage
            'es',        //$destinationLanguage
            null,             //$tagHandling
            null, //$ignoreTags
            'more'                         //$formality
        );
    }


    /**
     * Test to Test the Tag-Handling.
     * @throws DeepLException
     */
    public function testTranslateWithHTML()
    {
        if (self::$authKey === false) {
            $this->markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl           = new DeepL(self::$authKey);
        $textToTranslate = array(
            'Hello World<strong>This should stay the same</strong>',
            'Another Text<br> new line <p>this is a paragraph</p>'
        );

        $expectedArray   = array(
            array(
                'detected_source_language' => "EN",
                'text'                     => "Hallo Welt<strong>This should stay the same</strong>",
            ),
            array(
                'detected_source_language' => "EN",
                'text'                     => "Ein weiterer Text neue Zeile <p>dies ist ein Absatz</p>",
            ),

        );

        $translatedText = $deepl->translate(
            $textToTranslate,
            'en',           //$sourceLanguage
            'de',        //$destinationLanguage
            'xml',             //$tagHandling
            array('strong'), //$ignoreTags
            'less',                         //$formality
            1,
            0,
            array('br'),
            1,
            array('p')
        );

        $this->assertEquals($expectedArray, $translatedText);
    }

    /**
     * Test translate() with unsupported sourceLanguage
     */
    public function testTranslateWithNotSupportedSourceLanguage()
    {
        if (self::$authKey === false) {
            $this->markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl = new DeepL(self::$authKey);

        $this->expectException('\BabyMarkt\DeepL\DeepLException');
        $deepl->translate('some txt', 'dk', 'de');
    }

    /**
     * Test translate() with unsupported targetLanguage
     */
    public function testTranslateWithNotSupportedDestinationLanguage()
    {
        if (self::$authKey === false) {
            $this->markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl = new DeepL(self::$authKey);

        $this->expectException('\BabyMarkt\DeepL\DeepLException');
        $deepl->translate('some txt', 'en', 'dk');
    }
}
