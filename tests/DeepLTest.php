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
     * Get protected method
     *
     * @param $className
     * @param $methodName
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
        return;

        $authKey    = 'INSERT YOUR AUTH KEY HERE';
        $germanText = 'Hallo Welt';

        $deepl     = new DeepL($authKey);

        $translatedText = $deepl->translate($germanText);

        $this->assertEquals('Hello World', $translatedText);
    }

    /**
     * Test translate() with tag handling success
     *
     * TEST REQUIRES VALID DEEPL AUTH KEY!!
     */
    public function testTranslateTagHandlingSuccess()
    {
        return;

        $authKey    = 'INSERT YOUR AUTH KEY HERE';
        $englishText = '<strong>text to translate</strong>';

        $deepl     = new DeepL($authKey);

        $translatedText = $deepl->translate($germanText, 'en', 'de', ['xml']);

        $this->assertEquals('<strong>Text zu übersetzen</strong>', $translatedText);
    }

    /**
     * Test translate()
     *
     * TEST REQUIRES VALID DEEPL AUTH KEY!!
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