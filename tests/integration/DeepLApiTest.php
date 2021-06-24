<?php

namespace BabyMarkt\DeepL\integration;

use BabyMarkt\DeepL\DeepL;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

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
    protected static $authKey = false;

    /**
     * Proxy URL
     * @var bool|string
     */
    private static $proxy;

    /**
     * Proxy Credentials
     * @var bool|string
     */
    private static $proxyCredentials;

    /**
     * Setup DeepL Auth Key.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $authKey = getenv('DEEPL_AUTH_KEY');
        $proxy = getenv('HTTP_PROXY');
        $proxyCredentials = getenv('HTTP_PROXY_CREDENTIALS');

        if ($authKey === false) {
            return;
        }

        self::$authKey = $authKey;
        self::$proxy = $proxy;
        self::$proxyCredentials = $proxyCredentials;
    }

    /**
     * Get protected method
     *
     * @param $className
     * @param $methodName
     *
     * @throws \ReflectionException
     *
     * @return \ReflectionMethod
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
     */
    public function testTranslateSuccess()
    {
        if (self::$authKey === false) {
            self::markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl = new DeepL(self::$authKey);

        $germanText     = 'Hallo Welt';
        $expectedText   = 'Hello world';

        $translatedText = $deepl->translate($germanText);

        self::assertEquals($expectedText, $translatedText[0]['text']);
    }

    /**
     * Test translate() success with v1 API
     */
    public function testTranslateV1Success()
    {
        if (self::$authKey === false) {
            self::markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl = new DeepL(self::$authKey, 1);

        $germanText     = 'Hallo Welt';
        $expectedText   = 'Hello world';

        $translatedText = $deepl->translate($germanText);

        self::assertEquals($expectedText, $translatedText[0]['text']);
    }

    /**
     * Test translate() with unknown API-Version
     */
    public function testTranslateWrongVersion()
    {
        if (self::$authKey === false) {
            self::markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
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
            self::markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
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

        self::assertEquals($expectedText, $translatedText[0]['text']);
    }

    /**
     * Test translate() with tag ignored success
     */
    public function testTranslateIgnoreTagsSuccess()
    {
        if (self::$authKey === false) {
            self::markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
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

        self::assertEquals($expectedText, $translatedText[0]['text']);
    }

    /**
     * Test usage() has certain array-keys
     */
    public function testUsage()
    {
        if (self::$authKey === false) {
            self::markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl    = new DeepL(self::$authKey);
        $response = $deepl->usage();

        self::assertArrayHasKey('character_count', $response);
        self::assertArrayHasKey('character_limit', $response);
    }

    /**
     * Test languages() has certain array-keys
     */
    public function testLanguages()
    {
        if (self::$authKey === false) {
            self::markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl    = new DeepL(self::$authKey);
        $response = $deepl->languages();

        foreach ($response as $language) {
            self::assertArrayHasKey('language', $language);
            self::assertArrayHasKey('name', $language);
        }
    }

    /**
     * Test languages() can return the source-languages
     */
    public function testLanguagesSource()
    {
        if (self::$authKey === false) {
            self::markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl    = new DeepL(self::$authKey);
        $response = $deepl->languages('source');

        foreach ($response as $language) {
            self::assertArrayHasKey('language', $language);
            self::assertArrayHasKey('name', $language);
        }
    }

    /**
     * Test languages()  can return the targe-languages
     */
    public function testLanguagesTarget()
    {
        if (self::$authKey === false) {
            self::markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl    = new DeepL(self::$authKey);
        $response = $deepl->languages('target');

        foreach ($response as $language) {
            self::assertArrayHasKey('language', $language);
            self::assertArrayHasKey('name', $language);
        }
    }

    /**
     * Test languages() will fail with wrong Parameter
     */
    public function testLanguagesFail()
    {
        if (self::$authKey === false) {
            self::markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl    = new DeepL(self::$authKey);

        $this->expectException('\BabyMarkt\DeepL\DeepLException');

        $deepl->languages('fail');
    }

    /**
     * Test translate() with all Params
     */
    public function testTranslateWithAllParams()
    {
        if (self::$authKey === false) {
            self::markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
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

        self::assertEquals($expectedText, $translatedText[0]['text']);
    }

    /**
     * Test translate() with all Params
     */
    public function testWithProxy()
    {
        if (self::$authKey === false) {
            $this->markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        if (self::$proxy === false) {
            // The test would succeed with $proxy === false but it wouln't mean anything.
            $this->markTestSkipped('Proxy is not configured.');
        }

        $deepl = new DeepL(self::$authKey);
        $deepl->setProxy(self::$proxy);
        $deepl->setProxyCredentials(self::$proxyCredentials);

        $englishText  = 'please translate this text';
        $expectedText = 'Bitte übersetzen Sie diesen Text';

        $translatedText = $deepl->translate($englishText, 'en', 'de');

        $this->assertEquals($expectedText, $translatedText[0]['text']);
    }

    /**
     * Test translate() with all Params
     */
    public function testCustomTimeout()
    {
        $deepl = new DeepL(self::$authKey, 2, '10.255.255.1'); // non routable IP, should timeout.
        $deepl->setTimeout(2);

        $start = time();
        try {
            $deepl->translate('some text');
        } catch (\Exception $e) {
            $time = time() - $start;
            $this->assertLessThan(4, $time);
        }
    }

    /**
     * Test translate() $formality
     */
    public function testTranslateFormality()
    {
        if (self::$authKey === false) {
            self::markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl = new DeepL(self::$authKey);

        $englishText    = '<strong>text to do not translate</strong><p>please translate this text</p>';
        $expectedText   = '<strong>Nicht zu übersetzender Text</strong><p>Bitte übersetze diesen Text</p>';
        $translatedText = $deepl->translate(
            $englishText,
            'en',           //$sourceLanguage
            'de',        //$destinationLanguage
            null,             //$tagHandling
            null, //$ignoreTags
            'less'                         //$formality
        );

        self::assertEquals($expectedText, $translatedText[0]['text']);
    }

    /**
     * Test to Test the Tag-Handling.
     */
    public function testTranslateWithHTML()
    {
        if (self::$authKey === false) {
            self::markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
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
                'text'                     => "Ein weiterer Text<br>neue Zeile <p>dies ist ein Absatz</p></br> ",
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

        self::assertEquals($expectedArray, $translatedText);
    }

    /**
     * Test translate() with unsupported sourceLanguage
     */
    public function testTranslateWithNotSupportedSourceLanguage()
    {
        if (self::$authKey === false) {
            self::markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
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
            self::markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl = new DeepL(self::$authKey);

        $this->expectException('\BabyMarkt\DeepL\DeepLException');
        $deepl->translate('some txt', 'en', 'dk');
    }
}
