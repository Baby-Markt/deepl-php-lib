<?php

namespace BabyMarkt\DeepL\unit;

use BabyMarkt\DeepL\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testBuildQueryWithNulledArguments()
    {
        $authKey        = '123456';
        $args           =
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
            );
        $expectedString = '';
        $testSubject    = new Client($authKey);
        $result         = $testSubject->buildQuery($args);

        self::assertEquals($expectedString, $result);
    }

    public function testBuildQueryWithMinimalArguments()
    {
        $authKey        = '123456';
        $args           = array(
            'text'        => 'text',
            'source_lang' => 'de',
            'target_lang' => 'en',
        );
        $expectedString = 'text=text&source_lang=de&target_lang=en';
        $testSubject    = new Client($authKey);
        $result         = $testSubject->buildQuery($args);

        self::assertEquals($expectedString, $result);
    }

    public function testBuildQueryWithEmptyArguments()
    {
        $authKey        = '123456';
        $args           = array(
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
        );
        $expectedString = 'text=text&source_lang=en&target_lang=de&splitting_tags=&non_splitting_tags=&ignore_tags=';
        $testSubject    = new Client($authKey);
        $result         = $testSubject->buildQuery($args);

        self::assertEquals($expectedString, $result);
    }

    public function testBuildQueryWithAllArguments()
    {
        $authKey = '123456';
        $args    = array(
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

        $testSubject = new Client($authKey);
        $result      = $testSubject->buildQuery($args);

        self::assertEquals($expectation, $result);
    }

    public function testBuildQueryWithAllArgumentsAndPreserveFormattingZero()
    {
        $authKey = '123456';
        $args    = array(
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
        $testSubject = new Client($authKey);
        $result      = $testSubject->buildQuery($args);

        self::assertEquals($expectation, $result);
    }

    public function testBuildQueryWithAllArgumentsAndMultipleTexts()
    {
        $authKey     = '123456';
        $args        = array(
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

        $testSubject = new Client($authKey);
        $result      = $testSubject->buildQuery($args);

        self::assertEquals($expectation, $result);
    }

    /**
     * Test buildBaseUrl() with own host
     */
    public function testBuildBaseUrlHost()
    {
        $authKey     = '123456';
        $host        = 'myownhost.dev';
        $expectedUrl = 'https://' . $host . '/v2/translate';
        $testSubject = new Client($authKey, 2, $host);
        $result      = $testSubject->buildBaseUrl();

        self::assertEquals($expectedUrl, $result);
    }

    /**
     * Test buildBaseUrl()
     */
    public function testBuildBaseUrl()
    {
        $authKey     = '123456';
        $expectedUrl = 'https://api.deepl.com/v2/translate';
        $testSubject = new Client($authKey);
        $result      = $testSubject->buildBaseUrl();

        self::assertEquals($expectedUrl, $result);
    }
}
