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
    public function testBuildBaseUrl()
    {
        $authKey     = '123456';
        $expectedUrl = 'https://api.deepl.com/v2/translate?auth_key='.$authKey;
        $deepl       = new DeepL($authKey);
        $buildUrl    = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildBaseUrl');
        $return      = $buildUrl->invokeArgs($deepl, array());

        $this->assertEquals($expectedUrl, $return);
    }

    /**
     * Test buildBaseUrl() with own host
     */
    public function testBuildBaseUrlHost()
    {
        $authKey        = '123456';
        $host           = 'myownhost.dev';
        $expectedString = 'https://'.$host.'/v2/translate?auth_key='.$authKey;
        $deepl          = new DeepL($authKey, 2, $host);
        $buildUrl       = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildBaseUrl');
        $return         = $buildUrl->invokeArgs($deepl, array());

        $this->assertEquals($expectedString, $return);
    }

    /**
     * Test buildQuery with empty Arguemtns
     */
    public function testBuildQueryWithNulledArguments()
    {
        $authKey        = '123456';
        $args           = array(null,null,null,null,null,null,null,null,null,null,null);
        $expectedString = '';
        $deepl          = new DeepL($authKey);
        $buildQuery     = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildQuery');
        $return         = $buildQuery->invokeArgs($deepl, $args);

        $this->assertEquals($expectedString, $return);
    }

    public function testBuildQueryWithMinimalArguments()
    {
        $authKey        = '123456';
        $args           = array('text','en','de',null,null,null,null,null,null,null,null);
        $expectedString = 'text=text&source_lang=de&target_lang=en';
        $deepl          = new DeepL($authKey);
        $buildQuery     = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildQuery');
        $return         = $buildQuery->invokeArgs($deepl, $args);

        $this->assertEquals($expectedString, $return);
    }

    public function testBuildQueryWithEmptyArguments()
    {
        $authKey        = '123456';
        $args           = array('text','en','de',array(),array(),array(),'','','','','');
        $expectedString = 'text=text&source_lang=de&target_lang=en';
        $deepl          = new DeepL($authKey);
        $buildQuery     = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildQuery');
        $return         = $buildQuery->invokeArgs($deepl, $args);

        $this->assertEquals($expectedString, $return);
    }

    public function testBuildQueryWithAllArguments()
    {
        $authKey        = '123456';
        $args           = array(
            'text',
            'en',
            'de',
            array('p','h1'),
            array('br','strong'),
            array('href','i'),
            'xml',
            'default',
            'nonewlines',
            1,
            0
        );

        $expectation = http_build_query(array(
            'text' => 'text',
            'source_lang' => 'de',
            'target_lang' => 'en',
            'splitting_tags' => 'p,h1',
            'non_splitting_tags' => 'br,strong',
            'ignore_tags' => 'href,i',
            'tag_handling' => 'xml',
            'formality' => 'default',
            'split_sentences' => 'nonewlines',
            'preserve_formatting' => 1,
            'outline_detection' => 0
        ));

        $deepl          = new DeepL($authKey);
        $buildQuery     = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildQuery');
        $return         = $buildQuery->invokeArgs($deepl, $args);

        $this->assertEquals($expectation, $return);
    }

    public function testBuildQueryWithAllArgumentsAndPreserveFormattingZero()
    {
        $authKey        = '123456';
        $args = array(
            'text',
            'en',
            'de',
            array('p', 'h1'),
            array('br', 'strong'),
            array('href', 'i'),
            'xml',
            'default',
            'nonewlines',
            0,
            0,
        );

        $expectation = http_build_query(array(
            'text' => 'text',
            'source_lang' => 'de',
            'target_lang' => 'en',
            'splitting_tags' => 'p,h1',
            'non_splitting_tags' => 'br,strong',
            'ignore_tags' => 'href,i',
            'tag_handling' => 'xml',
            'formality' => 'default',
            'split_sentences' => 'nonewlines',
            'outline_detection' => 0
        ));
        $deepl          = new DeepL($authKey);
        $buildQuery     = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildQuery');
        $return         = $buildQuery->invokeArgs($deepl, $args);

        $this->assertEquals($expectation, $return);
    }

    public function testBuildQueryWithAllArgumentsAndOutlineDetectionOne()
    {
        $authKey        = '123456';
        $args = array(
            'text',
            'en',
            'de',
            array('p', 'h1'),
            array('br', 'strong'),
            array('href', 'i'),
            'xml',
            'default',
            'nonewlines',
            0,
            1,
        );

        $expectation = http_build_query(array(
            'text' => 'text',
            'source_lang' => 'de',
            'target_lang' => 'en',
            'splitting_tags' => 'p,h1',
            'non_splitting_tags' => 'br,strong',
            'ignore_tags' => 'href,i',
            'tag_handling' => 'xml',
            'formality' => 'default',
            'split_sentences' => 'nonewlines',
        ));
        $deepl          = new DeepL($authKey);
        $buildQuery     = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildQuery');
        $return         = $buildQuery->invokeArgs($deepl, $args);

        $this->assertEquals($expectation, $return);
    }

    public function testBuildQueryWithAllArgumentsAndMultipleTexts()
    {
        $authKey        = '123456';
        $args           = array(
            array('text','more text','even more text'),
            'en',
            'de',
            array('p','h1'),
            array('br','strong'),
            array('href','i'),
            'xml',
            'default',
            'nonewlines',
            1,
            0
        );
        $expectation = '&text=text&text=more%20text&text=even%20more%20text&';
        $expectation .= http_build_query(array(
            'source_lang' => 'de',
            'target_lang' => 'en',
            'splitting_tags' => 'p,h1',
            'non_splitting_tags' => 'br,strong',
            'ignore_tags' => 'href,i',
            'tag_handling' => 'xml',
            'formality' => 'default',
            'split_sentences' => 'nonewlines',
            'preserve_formatting' => 1,
            'outline_detection' => 0
        ));

        $deepl          = new DeepL($authKey);
        $buildQuery     = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildQuery');
        $return         = $buildQuery->invokeArgs($deepl, $args);

        $this->assertEquals($expectation, $return);
    }
}
