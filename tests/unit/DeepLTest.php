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


    public function testBuildUrlWithAllParams()
    {
        $authKey = '123456';
        // created with DeepL Simulator: https://www.deepl.com/docs-api/simulator/
        $expectedString  = 'https://api.deepl.com/v2/translate?auth_key='.$authKey;
        $expectedString .= '&source_lang=en&target_lang=de&tag_handling=xml&ignore_tags=html%2Chtml5%2Chtml6';
        $expectedString .= '&formality=more&split_sentences=nonewlines&preserve_formatting=1';
        $expectedString .= '&non_splitting_tags=href%2Chref2&outline_detection=0&splitting_tags=p%2Cbr';

        $deepl    = new DeepL($authKey);
        $buildUrl = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildUrl');
        $args     = array(
            'en',                          //$sourceLanguage
            'de',                          //$destinationLanguage
            'xml',                         //$tagHandling
            array('html','html5','html6'), //$ignoreTags
            'more',                        //$formality
            'translate',                   //$resource
            'nonewlines',                  //$splitSentences
            1,                             //$preserveFormatting
            array('href','href2'),         //$nonSplittingTags
            0,                             //$outlineDetection
            array('p','br')                //$splittingTags
        );

        $return = $buildUrl->invokeArgs($deepl, $args);

        $this->assertEquals($expectedString, $return);
    }
}
