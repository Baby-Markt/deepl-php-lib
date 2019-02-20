<?php

namespace BabyMarkt\DeepL;

/**
 * Class DeepLTest
 *
 * @package BabyMarkt\DeepL
 */
class DeepLTest extends \PHPUnit_Framework_TestCase
{
    /**
     * DeepL Auth Key (Set if you want to test all methods)
     *
     * @var string
     */
    private $authKey = '';

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
        $class = new \ReflectionClass($className);
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
        $expectedString = 'https://api.deepl.com/v1/translate?auth_key=123456&source_lang=de&target_lang=en';

        $authKey = '123456';
        $deepl   = new DeepL($authKey);

        $buildUrl = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildUrl');

        $return = $buildUrl->invokeArgs($deepl, array('de', 'en'));

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
     * Test translate() calls
     */
    public function testTranslate()
    {
        return;

        $mock = $this->getMockBuilder('\BabyMarkt\DeepL\DeepL');

        // TODO: test if translate methods calls correct methods
    }

    /**
     * Test translate() success
     *
     * TEST REQUIRES VALID DEEPL AUTH KEY!!
     */
    public function testTranslateSuccess()
    {
        if (!$this->authKey) {
            return;
        }

        $deepl = new DeepL($this->authKey);

        $germanText     = 'Hallo Welt';
        $expectedText   = 'Hello World';

        $translatedText = $deepl->translate($germanText);

        $this->assertEquals($expectedText, $translatedText);
    }

    /**
     * Test translate() success with v2 API
     *
     * TEST REQUIRES VALID DEEPL AUTH KEY!!
     */
    public function testTranslateV2Success()
    {
        if (!$this->authKey) {
            return;
        }

        $deepl = new DeepL($this->authKey, 2);

        $germanText     = 'Hallo Welt';
        $expectedText   = 'Hello World';

        $translatedText = $deepl->translate($germanText);

        $this->assertEquals($expectedText, $translatedText);
    }

    /**
     * Test translate() with tag handling success
     *
     * TEST REQUIRES VALID DEEPL AUTH KEY!!
     */
    public function testTranslateTagHandlingSuccess()
    {
        if (!$this->authKey) {
            return;
        }

        $deepl = new DeepL($this->authKey);

        $englishText  = '<strong>text to translate</strong>';
        $expectedText = '<strong>zu übersetzender Text</strong>';

        $translatedText = $deepl->translate(
            $englishText,
            'en',
            'de',
            array('xml')
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
        if (!$this->authKey) {
            return;
        }

        $deepl = new DeepL($this->authKey);

        $englishText  = '<strong>text to do not translate</strong><p>text to translate</p>';
        $expectedText = '<strong>Text, der nicht übersetzt werden soll</strong><p>zu übersetzender Text</p>';

        $translatedText = $deepl->translate(
            $englishText,
            'en',
            'de',
            array('xml'),
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

        $deepl     = new DeepL($authKey);

        $this->setExpectedException('\BabyMarkt\DeepL\DeepLException');

        $translatedText = $deepl->translate($germanText);
    }
}