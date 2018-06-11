<?php

namespace BabyMarkt\DeepL;

class DeepLTest extends \PHPUnit_Framework_TestCase
{
    protected static function getMethod($className, $methodName)
    {
        $class = new \ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }

    public function testCheckLanguages()
    {
        $authKey = '123456';
        $deepl   = new DeepL($authKey);

        $checkLanguages = self::getMethod('\BabyMarkt\DeepL\DeepL', 'checkLanguages');

        $return = $checkLanguages->invokeArgs($deepl, array('de', 'en'));

        $this->assertTrue($return);
    }

    public function testCheckLanguagesSourceLanguageException()
    {
        $authKey = '123456';
        $deepl   = new DeepL($authKey);

        $checkLanguages = self::getMethod('\BabyMarkt\DeepL\DeepL', 'checkLanguages');

        $this->setExpectedException(DeepLException::class);

        $checkLanguages->invokeArgs($deepl, array('fo', 'en'));
    }

    public function testCheckLanguagesDestinationLanguageException()
    {
        $authKey = '123456';
        $deepl   = new DeepL($authKey);

        $checkLanguages = self::getMethod('\BabyMarkt\DeepL\DeepL', 'checkLanguages');

        $this->setExpectedException(DeepLException::class);

        $checkLanguages->invokeArgs($deepl, array('de', 'fo'));
    }

    public function testBuildUrl()
    {
        $expectedString = 'https://api.deepl.com/v1/translate?auth_key=123456&text=Hallo%20Welt&source_lang=de&target_lang=en';

        $authKey = '123456';
        $deepl   = new DeepL($authKey);

        $buildUrl = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildUrl');

        $return = $buildUrl->invokeArgs($deepl, array('Hallo Welt', 'de', 'en'));

        $this->assertEquals($expectedString, $return);
    }
}