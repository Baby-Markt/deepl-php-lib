<?php

namespace BabyMarkt\DeepL\integration;

use BabyMarkt\DeepL\Glossary;
use DateTime;
use PHPUnit\Framework\TestCase;

class GlossaryTest extends TestCase
{
    /**
     * DeepL Auth Key.
     *
     * @var bool|string
     */
    protected static $authKey = false;

    /**
     * DeepL Auth Key.
     *
     * @var string
     */
    protected static $apiHost;

    /**
     * Setup DeepL Auth Key.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $authKey = getenv('DEEPL_AUTH_KEY');
        $apiHost = getenv('DEEPL_HOST') ?: 'api-free.deepl.com';

        if ($authKey === false) {
            self::markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        self::$authKey = $authKey;
        self::$apiHost = $apiHost;
    }

    public static function tearDownAfterClass(): void
    {
        $glossaryAPI = new Glossary(self::$authKey, 2, self::$apiHost);

        $glossariesResponse = $glossaryAPI->listGlossaries();

        $fiveMinutesAgo = new DateTime(
            date(
                'Y-m-d H:i:s',
                time() - 60 * 5
            )
        );

        // Cleanup old test fixtures in the deepl API. The 5 minute threashold is used to prevent parallel tests
        // execution intervention on the CI server.
        foreach ($glossariesResponse['glossaries'] as $glossary) {
            if (new DateTime($glossary['creation_time']) < $fiveMinutesAgo) {
                $glossaryAPI->deleteGlossary($glossary['glossary_id']);
            }
        }
    }

    /**
     * Test Glossary creation
     */
    public function testCreateGlossary()
    {
        if (self::$authKey === false) {
            self::markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl    = new Glossary(self::$authKey, 2, self::$apiHost);
        $entries  = ['Hallo' => 'Hello'];
        $glossary = $deepl->createGlossary('test', $entries, 'de', 'en');

        self::assertArrayHasKey('glossary_id', $glossary);

        return $glossary['glossary_id'];
    }

    /**
     * Test Glossary listing
     */
    public function testListGlossaries()
    {
        if (self::$authKey === false) {
            self::markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl      = new Glossary(self::$authKey, 2, self::$apiHost);
        $glossaries = $deepl->listGlossaries();

        self::assertNotEmpty($glossaries['glossaries']);
    }

    /**
     * Test listing information about a glossary
     *
     * @depends testCreateGlossary
     */
    public function testGlossaryInformation($glossaryId)
    {
        if (self::$authKey === false) {
            self::markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl        = new Glossary(self::$authKey, 2, self::$apiHost);
        $information = $deepl->glossaryInformation($glossaryId);

        self::assertArrayHasKey('glossary_id', $information);
    }

    /**
     * Test listing entries in a glossary
     *
     * @depends testCreateGlossary
     */
    public function testGlossaryEntries($glossaryId)
    {
        if (self::$authKey === false) {
            self::markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl   = new Glossary(self::$authKey, 2, self::$apiHost);
        $entries = $deepl->glossaryEntries($glossaryId);

        self::assertEquals($entries, ['Hallo' => 'Hello']);
    }

    /**
     * Test deleting a glossary
     *
     * @depends testCreateGlossary
     */
    public function testDeleteGlossary($glossaryId)
    {
        if (self::$authKey === false) {
            self::markTestSkipped('DeepL Auth Key (DEEPL_AUTH_KEY) is not configured.');
        }

        $deepl    = new Glossary(self::$authKey, 2, self::$apiHost);
        $response = $deepl->deleteGlossary($glossaryId);

        self::assertNull($response);
    }
}
