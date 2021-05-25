<?php

namespace BabyMarkt\DeepL\unit;

use BabyMarkt\DeepL\DeepL;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Class DeepLTest
 *
 * @package BabyMarkt\DeepL
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class DeepLTest extends TestCase
{
    /**
     * Get protected method
     *
     * @param $className
     * @param $methodName
     *
     * @return \ReflectionMethod
     * @throws \ReflectionException
     *
     */
    protected static function getMethod($className, $methodName)
    {
        $class  = new ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * Test translate()
     */
    public function testTranslateException()
    {
        $authKey    = '123456';
        $germanText = 'Hallo Welt';
        $deepl      = new DeepL($authKey);

        $this->expectException('\BabyMarkt\DeepL\DeepLException');

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

        self::assertEquals($expectedUrl, $return);
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

        self::assertEquals($expectedString, $return);
    }


    /**
     * Test buildQuery with empty Arguments
     */
    public function testBuildQueryWithNulledArguments()
    {
        $authKey        = '123456';
        $args           = array(
            array(
                'text'                => null,
                'source_lang'         => null,
                'target_lang'         => null,
                'splitting_tags'      => null,
                'non_splitting_tags'  => null,
                'ignore_tags'         => null,
                'tag_handling'        => null,
                'formality'           => null,
                'split_sentences'     => null,
                'preserve_formatting' => null,
                'outline_detection'   => null,
            ),
        );
        $expectedString = '';
        $deepl          = new DeepL($authKey);
        $buildQuery     = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildQuery');
        $return         = $buildQuery->invokeArgs($deepl, $args);

        self::assertEquals($expectedString, $return);
    }

    public function testBuildQueryWithMinimalArguments()
    {
        $authKey        = '123456';
        $args           = array(
            array(
                'text'        => 'text',
                'source_lang' => 'de',
                'target_lang' => 'en',
            ),
        );
        $expectedString = 'text=text&source_lang=de&target_lang=en';
        $deepl          = new DeepL($authKey);
        $buildQuery     = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildQuery');
        $return         = $buildQuery->invokeArgs($deepl, $args);

        self::assertEquals($expectedString, $return);
    }

    public function testBuildQueryWithEmptyArguments()
    {
        $authKey        = '123456';
        $args           = array(
            array(
                'text'                => 'text',
                'source_lang'         => 'en',
                'target_lang'         => 'de',
                'splitting_tags'      => array(),
                'non_splitting_tags'  => array(),
                'ignore_tags'         => array(),
                'tag_handling'        => null,
                'formality'           => null,
                'split_sentences'     => null,
                'preserve_formatting' => null,
                'outline_detection'   => null,
            ),
        );
        $expectedString = 'text=text&source_lang=en&target_lang=de&splitting_tags=&non_splitting_tags=&ignore_tags=';
        $deepl          = new DeepL($authKey);
        $buildQuery     = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildQuery');
        $return         = $buildQuery->invokeArgs($deepl, $args);

        self::assertEquals($expectedString, $return);
    }

    public function testBuildQueryWithAllArguments()
    {
        $authKey = '123456';
        $args    = array(
            array(
                'text'                => 'text',
                'source_lang'         => 'de',
                'target_lang'         => 'en',
                'splitting_tags'      => array('p', 'h1'),
                'non_splitting_tags'  => array('br', 'strong'),
                'ignore_tags'         => array('href', 'i'),
                'tag_handling'        => 'xml',
                'formality'           => 'default',
                'split_sentences'     => 'nonewlines',
                'preserve_formatting' => 1,
                'outline_detection'   => 0,
            ),
        );

        $expectation = http_build_query(
            array(
                'text'                => 'text',
                'source_lang'         => 'de',
                'target_lang'         => 'en',
                'splitting_tags'      => 'p,h1',
                'non_splitting_tags'  => 'br,strong',
                'ignore_tags'         => 'href,i',
                'tag_handling'        => 'xml',
                'formality'           => 'default',
                'split_sentences'     => 'nonewlines',
                'preserve_formatting' => 1,
                'outline_detection'   => 0,
            )
        );

        $deepl      = new DeepL($authKey);
        $buildQuery = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildQuery');
        $return     = $buildQuery->invokeArgs($deepl, $args);

        self::assertEquals($expectation, $return);
    }

    public function testBuildQueryWithAllArgumentsAndPreserveFormattingZero()
    {
        $authKey = '123456';
        $args    = array(
            array(
                'text'               => 'text',
                'source_lang'        => 'de',
                'target_lang'        => 'en',
                'splitting_tags'     => array('p', 'h1'),
                'non_splitting_tags' => array('br', 'strong'),
                'ignore_tags'        => array('href', 'i'),
                'tag_handling'       => 'xml',
                'formality'          => 'default',
                'split_sentences'    => 'nonewlines',
                'outline_detection'  => 0,
            ),
        );

        $expectation = http_build_query(
            array(
                'text'               => 'text',
                'source_lang'        => 'de',
                'target_lang'        => 'en',
                'splitting_tags'     => 'p,h1',
                'non_splitting_tags' => 'br,strong',
                'ignore_tags'        => 'href,i',
                'tag_handling'       => 'xml',
                'formality'          => 'default',
                'split_sentences'    => 'nonewlines',
                'outline_detection'  => 0,
            )
        );
        $deepl       = new DeepL($authKey);
        $buildQuery  = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildQuery');
        $return      = $buildQuery->invokeArgs($deepl, $args);

        self::assertEquals($expectation, $return);
    }

    public function testBuildQueryWithAllArgumentsAndMultipleTexts()
    {
        $authKey     = '123456';
        $args        = array(
            array(
                'text'                => array('text', 'more text', 'even more text'),
                'source_lang'         => 'de',
                'target_lang'         => 'en',
                'splitting_tags'      => array('p', 'h1'),
                'non_splitting_tags'  => array('br', 'strong'),
                'ignore_tags'         => array('href', 'i'),
                'tag_handling'        => 'xml',
                'formality'           => 'default',
                'split_sentences'     => 'nonewlines',
                'preserve_formatting' => 1,
                'outline_detection'   => 0,
            ),
        );
        $expectation = '&text=text&text=more%20text&text=even%20more%20text&';
        $expectation .= http_build_query(
            array(
                'source_lang'         => 'de',
                'target_lang'         => 'en',
                'splitting_tags'      => 'p,h1',
                'non_splitting_tags'  => 'br,strong',
                'ignore_tags'         => 'href,i',
                'tag_handling'        => 'xml',
                'formality'           => 'default',
                'split_sentences'     => 'nonewlines',
                'preserve_formatting' => 1,
                'outline_detection'   => 0,
            )
        );

        $deepl      = new DeepL($authKey);
        $buildQuery = self::getMethod('\BabyMarkt\DeepL\DeepL', 'buildQuery');
        $return     = $buildQuery->invokeArgs($deepl, $args);

        self::assertEquals($expectation, $return);
    }

    public function testRemoveEmptyParamsWithMinimalArguments()
    {
        $authKey        = '123456';
        $args           = array(array(
            'text'                => 'text',
            'source_lang'         => 'de',
            'target_lang'         => 'en',
            'splitting_tags'      => null,
            'non_splitting_tags'  => null,
            'ignore_tags'         => null,
            'tag_handling'        => null,
            'formality'           => null,
            'split_sentences'     => null,
            'preserve_formatting' => null,
            'outline_detection'   => null
        ));
        $expectedString = array(
            'text'                => 'text',
            'source_lang'         => 'de',
            'target_lang'         => 'en'
        );
        $deepl          = new DeepL($authKey);
        $buildQuery     = self::getMethod('\BabyMarkt\DeepL\DeepL', 'removeEmptyParams');
        $return         = $buildQuery->invokeArgs($deepl, $args);

        self::assertEquals($expectedString, $return);
    }

    public function testRemoveEmptyParamsWithEmptyArguments()
    {
        $authKey        = '123456';
        $args           = array(array(
            'text'                => 'text',
            'source_lang'         => 'de',
            'target_lang'         => 'en',
            'splitting_tags'      => array(),
            'non_splitting_tags'  => array(),
            'ignore_tags'         => array(),
            'tag_handling'        => null,
            'formality'           => null,
            'split_sentences'     => null,
            'preserve_formatting' => null,
            'outline_detection'   => null
        ));
        $expectedString = array(
            'text'                => 'text',
            'source_lang'         => 'de',
            'target_lang'         => 'en'
        );
        $deepl          = new DeepL($authKey);
        $buildQuery     = self::getMethod('\BabyMarkt\DeepL\DeepL', 'removeEmptyParams');
        $return         = $buildQuery->invokeArgs($deepl, $args);

        self::assertEquals($expectedString, $return);
    }

    public function testRemoveEmptyParamsAllArgumentsAndPreserveFormattingZero()
    {
        $authKey        = '123456';
        $args           = array(array(
            'text'                => 'text',
            'source_lang'         => 'de',
            'target_lang'         => 'en',
            'splitting_tags'      => array('p','h1'),
            'non_splitting_tags'  => array('br','strong'),
            'ignore_tags'         => array('href','i'),
            'tag_handling'        => 'xml',
            'formality'           => 'default',
            'split_sentences'     => 'nonewlines',
            'preserve_formatting' => 0,
            'outline_detection'   => 0
        ));

        $expectation = array(
            'text' => 'text',
            'source_lang' => 'de',
            'target_lang' => 'en',
            'splitting_tags'      => array('p','h1'),
            'non_splitting_tags'  => array('br','strong'),
            'ignore_tags'         => array('href','i'),
            'tag_handling' => 'xml',
            'formality' => 'default',
            'split_sentences' => 'nonewlines',
            'outline_detection' => 0
        );
        $deepl          = new DeepL($authKey);
        $buildQuery     = self::getMethod('\BabyMarkt\DeepL\DeepL', 'removeEmptyParams');
        $return         = $buildQuery->invokeArgs($deepl, $args);

        self::assertSame($expectation, $return);
    }

    public function testRemoveEmptyParamsWithAllArguments()
    {
        $authKey        = '123456';
        $args           = array(array(
            'text'                => 'text',
            'source_lang'         => 'de',
            'target_lang'         => 'en',
            'splitting_tags'      => array('p','h1'),
            'non_splitting_tags'  => array('br','strong'),
            'ignore_tags'         => array('href','i'),
            'tag_handling'        => 'xml',
            'formality'           => 'default',
            'split_sentences'     => 'nonewlines',
            'preserve_formatting' => 1,
            'outline_detection'   => 0
        ));

        $expectation = array(
            'text' => 'text',
            'source_lang' => 'de',
            'target_lang' => 'en',
            'splitting_tags'      => array('p','h1'),
            'non_splitting_tags'  => array('br','strong'),
            'ignore_tags'         => array('href','i'),
            'tag_handling' => 'xml',
            'formality' => 'default',
            'split_sentences' => 'nonewlines',
            'preserve_formatting' => 1,
            'outline_detection' => 0
        );

        $deepl          = new DeepL($authKey);
        $buildQuery     = self::getMethod('\BabyMarkt\DeepL\DeepL', 'removeEmptyParams');
        $return         = $buildQuery->invokeArgs($deepl, $args);

        self::assertEquals($expectation, $return);
    }

    public function testRemoveEmptyParamsAllArgumentsAndOutlineDetectionOne()
    {
        $authKey        = '123456';
        $args           = array(array(
            'text'                => 'text',
            'source_lang'         => 'de',
            'target_lang'         => 'en',
            'splitting_tags'      => array('p','h1'),
            'non_splitting_tags'  => array('br','strong'),
            'ignore_tags'         => array('href','i'),
            'tag_handling'        => 'xml',
            'formality'           => 'default',
            'split_sentences'     => 'nonewlines',
            'preserve_formatting' => 0,
            'outline_detection'   => 1
        ));

        $expectation = array(
            'text' => 'text',
            'source_lang' => 'de',
            'target_lang' => 'en',
            'splitting_tags'      => array('p','h1'),
            'non_splitting_tags'  => array('br','strong'),
            'ignore_tags'         => array('href','i'),
            'tag_handling' => 'xml',
            'formality' => 'default',
            'split_sentences' => 'nonewlines'
        );
        $deepl          = new DeepL($authKey);
        $buildQuery     = self::getMethod('\BabyMarkt\DeepL\DeepL', 'removeEmptyParams');
        $return         = $buildQuery->invokeArgs($deepl, $args);

        self::assertEquals($expectation, $return);
    }

    /**
     * Test behaviour of translate() when tagHandling is an array
     */
    public function testTranslateExceptionTagHandling()
    {
        $authKey    = '123456';
        $germanText = 'Hallo Welt';
        $deepl      = new DeepL($authKey);

        $this->expectException('InvalidArgumentException');

        $deepl->translate($germanText, 'de', 'en', array('xml'));
    }
}
