<?php

namespace BabyMarkt\DeepL\unit;

use BabyMarkt\DeepL\DeepL;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

/**
 * Class DeepLTest
 *
 * @package BabyMarkt\DeepL
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class DeepLTest extends PHPUnit_Framework_TestCase
{
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

        $return = $buildUrl->invokeArgs($deepl, array());

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

        $return = $buildUrl->invokeArgs($deepl, array());

        $this->assertEquals($expectedString, $return);
    }
}
