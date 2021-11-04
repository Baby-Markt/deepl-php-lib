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
