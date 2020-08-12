<?php

namespace BabyMarkt\DeepL;

use ReflectionClass;

/**
 * Class DeepLTest
 *
 * @package BabyMarkt\DeepL
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class DeepLTest extends \PHPUnit_Framework_TestCase
{
    /**
     * DeepL Auth Key.
     *
     * @var bool|string
     */
    protected static $authKey = false;

    /**
     * Setup DeepL Auth Key.
     */
    public static function setUpBeforeClass()
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
     * Test checkLanguages()
     */
    public function testCheckLanguages()
    {
        $authKey = '123456';
        $deepl   = new DeepL($authKey);

        $checkLanguages = self::getMethod('\BabyMarkt\DeepL\DeepL', 'checkLanguages');

        $return = $checkLanguages->invokeArgs($deepl, array('de', 'en'));

        $this->assertTrue($return);
    }

    /**
     * Test checkLanguages() with exception for source language
     */
    public function testCheckLanguagesSourceLanguageException()
    {
        $authKey = '123456';
        $deepl   = new DeepL($authKey);

        $checkLanguages = self::getMethod('\BabyMarkt\DeepL\DeepL', 'checkLanguages');

        $this->setExpectedException('\BabyMarkt\DeepL\DeepLException');

        $checkLanguages->invokeArgs($deepl, array('fo', 'en'));
    }

    /**
     * Test checkLanguages() with exception for destination language
     */
    public function testCheckLanguagesDestinationLanguageException()
    {
        $authKey = '123456';
        $deepl   = new DeepL($authKey);

        $checkLanguages = self::getMethod('\BabyMarkt\DeepL\DeepL', 'checkLanguages');

        $this->setExpectedException('\BabyMarkt\DeepL\DeepLException');

        $checkLanguages->invokeArgs($deepl, array('de', 'fo'));
    }

    /**
     * Test buildUrl()
     */
    public function testBuildUrl()
    {
        $authKey = '123456';

        $expectedString = 'https://api.deepl.com/v2/translate?' . http_build_query(array(
            'auth_key' => $authKey,
            'source_lang' => 'de',
            'target_lang' => 'en',
            'formality' => 'default'
        ));

        $deepl = new DeepL($authKey);

        $buildUrl = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildUrl');

        $return = $buildUrl->invokeArgs($deepl, array('de', 'en'));

        $this->assertEquals($expectedString, $return);
    }

    /**
     * Test buildUrl()
     */
    public function testBuildUrlV1()
    {
        $authKey = '123456';

        $expectedString = 'https://api.deepl.com/v1/translate?' . http_build_query(array(
                'auth_key' => $authKey,
                'source_lang' => 'de',
                'target_lang' => 'en',
                'formality' => 'default'
            ));

        $deepl = new DeepL($authKey, 1);

        $buildUrl = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildUrl');

        $return = $buildUrl->invokeArgs($deepl, array('de', 'en'));

        $this->assertEquals($expectedString, $return);
    }

    /**
     * Test buildUrl()
     */
    public function testBuildUrlWithTags()
    {
        $authKey = '123456';
        $expectedString = 'https://api.deepl.com/v2/translate?' . http_build_query(array(
            'auth_key' => $authKey,
            'source_lang' => 'de',
            'target_lang' => 'en',
            'tag_handling' => 'xml',
            'ignore_tags' => 'x',
            'formality' => 'default'
        ));

        $deepl = new DeepL($authKey);

        $buildUrl = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildUrl');

        $return = $buildUrl->invokeArgs($deepl, array('de', 'en', 'xml', array('x')));

        $this->assertEquals($expectedString, $return);
    }

    /**
     * Test buildUrl()
     */
    public function testBuildUrlWithTagsV1()
    {
        $authKey = '123456';
        $expectedString = 'https://api.deepl.com/v1/translate?' . http_build_query(array(
                'auth_key' => $authKey,
                'source_lang' => 'de',
                'target_lang' => 'en',
                'tag_handling' => 'xml',
                'ignore_tags' => 'x',
                'formality' => 'default'
            ));

        $deepl = new DeepL($authKey, 1);

        $buildUrl = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildUrl');

        $return = $buildUrl->invokeArgs($deepl, array('de', 'en', 'xml', array('x')));

        $this->assertEquals($expectedString, $return);
    }

    /**
     * Test buildBody()
     */
    public function testBuildBody()
    {
        $expectedString = 'text=Hallo%20Welt';

        $authKey = '123456';
        $deepl   = new DeepL($authKey);

        $buildUrl = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildBody');

        $return = $buildUrl->invokeArgs($deepl, array('Hallo Welt'));

        $this->assertEquals($expectedString, $return);
    }

    /**
     * Test translate() success with v2 API
     *
     * TEST REQUIRES VALID DEEPL AUTH KEY!!
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

        $this->assertEquals($expectedText, $translatedText);
    }

    /**
     * Test translate() success with v1 API
     *
     * TEST REQUIRES VALID DEEPL AUTH KEY!!
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

        $this->assertEquals($expectedText, $translatedText);
    }

    /**
     * Test translate() success with default v2 API
     *
     * TEST REQUIRES VALID DEEPL AUTH KEY!!
     */
    public function testTranslateWrongVersion()
    {
        if (self::$authKey === false) {
            $this->markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }
        $germanText = 'Hallo Welt';
        $deepl      = new DeepL(self::$authKey, 3);

        $this->setExpectedException('\BabyMarkt\DeepL\DeepLException');

        $deepl->translate($germanText);
    }

    /**
     * Test translate() with tag handling success
     *
     * TEST REQUIRES VALID DEEPL AUTH KEY!!
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

        $this->assertEquals($expectedText, $translatedText);
    }

    /**
     * Test translate() with tag ignored success
     *
     * TEST REQUIRES VALID DEEPL AUTH KEY!!
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

        $this->assertEquals($expectedText, $translatedText);
    }

    /**
     * Test translate()
     */
    public function testTranslateException()
    {
        $authKey    = '123456';
        $germanText = 'Hallo Welt';
        $deepl      = new DeepL($authKey);

        $this->setExpectedException('\BabyMarkt\DeepL\DeepLException');

        $deepl->translate($germanText);
    }

    /**
     * Test usage() has certain array-keys
     *
     * TEST REQUIRES VALID DEEPL AUTH KEY!!
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
     *
     * TEST REQUIRES VALID DEEPL AUTH KEY!!
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
     * Test buildBaseUrl()
     */
    public function testbuildBaseUrl()
    {
        $authKey = '123456';

        $expectedString = 'https://api.deepl.com/v2/translate?'. http_build_query(array(
                'auth_key' => $authKey
            ));

        $deepl = new DeepL($authKey);

        $buildUrl = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildBaseUrl');

        $return = $buildUrl->invokeArgs($deepl,array());

        $this->assertEquals($expectedString, $return);
    }

    /**
     * Test buildBaseUrl() with own host
     */
    public function testbuildBaseUrlHost()
    {
        $authKey = '123456';
        $host    = 'myownhost.dev';

        $expectedString = 'https://'.$host.'/v2/translate?'. http_build_query(array(
                'auth_key' => $authKey
            ));

        $deepl = new DeepL($authKey, 2, $host);

        $buildUrl = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildBaseUrl');

        $return = $buildUrl->invokeArgs($deepl,array());

        $this->assertEquals($expectedString, $return);
    }
}
