<?php

namespace BabyMarkt\DeepL;

/**
 * Class Glossary capsules functionality provided by DeepL where one can configure a glossary/dictionary which
 * is used in translation.
 *
 * @see https://support.deepl.com/hc/en-us/articles/4405021321746-Managing-glossaries-with-the-DeepL-API
 */
class Glossary
{
    /**
     * API URL: glossaries
     */
    const API_URL_RESOURCE_GLOSSARIES = 'glossaries';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param                      $authKey
     * @param int                  $apiVersion
     * @param string               $host
     * @param ClientInterface|null $client
     */
    public function __construct($authKey, $apiVersion = 2, $host = 'api.deepl.com', ClientInterface $client = null)
    {
        $this->client = $client ?? new Client($authKey, $apiVersion, $host);
    }

    /**
     * Calls the glossary-Endpoint and return Json-response as an array
     *
     * @return array
     *
     * @throws DeepLException
     */
    public function listGlossaries()
    {
        return $this->client->request($this->client->buildBaseUrl(self::API_URL_RESOURCE_GLOSSARIES), '', 'GET');
    }

    /**
     * Creates a glossary, entries must be formatted as [sourceText => entryText] e.g: ['Hallo' => 'Hello']
     *
     * @param string $name
     * @param array $entries
     * @param string $sourceLang
     * @param string $targetLang
     * @param string $entriesFormat
     *
     * @return array|null
     *
     * @throws DeepLException
     */
    public function createGlossary(
        string $name,
        array $entries,
        string $sourceLang = 'de',
        string $targetLang = 'en',
        string $entriesFormat = 'tsv'
    ) {
        $formattedEntries = "";
        foreach ($entries as $source => $target) {
            $formattedEntries .= sprintf("%s\t%s\n", $source, $target);
        }

        $paramsArray = [
            'name' => $name,
            'source_lang'    => $sourceLang,
            'target_lang'    => $targetLang,
            'entries'        => $formattedEntries,
            'entries_format' => $entriesFormat
        ];

        $url  = $this->client->buildBaseUrl(self::API_URL_RESOURCE_GLOSSARIES);
        $body = $this->client->buildQuery($paramsArray);

        return $this->client->request($url, $body);
    }

    /**
     * Deletes a glossary
     *
     * @param string $glossaryId
     *
     * @return array|null
     *
     * @throws DeepLException
     */
    public function deleteGlossary(string $glossaryId)
    {
        $url = $this->client->buildBaseUrl(self::API_URL_RESOURCE_GLOSSARIES);
        $url .= "/$glossaryId";

        return $this->client->request($url, '', 'DELETE');
    }

    /**
     * Gets information about a glossary
     *
     * @param string $glossaryId
     *
     * @return array|null
     *
     * @throws DeepLException
     */
    public function glossaryInformation(string $glossaryId)
    {
        $url  = $this->client->buildBaseUrl(self::API_URL_RESOURCE_GLOSSARIES);
        $url .= "/$glossaryId";

        return $this->client->request($url, '', 'GET');
    }

    /**
     * Fetch glossary entries and format them as associative array [source => target]
     *
     * @param string $glossaryId
     *
     * @return array
     *
     * @throws DeepLException
     */
    public function glossaryEntries(string $glossaryId)
    {
        $url = $this->client->buildBaseUrl(self::API_URL_RESOURCE_GLOSSARIES);
        $url .= "/$glossaryId/entries";

        $response = $this->client->request($url, '', 'GET');

        $entries = [];
        if (!empty($response)) {
            $allEntries = preg_split('/\n/', $response);
            foreach ($allEntries as $entry) {
                $sourceAndTarget = preg_split('/\s+/', rtrim($entry));
                if (isset($sourceAndTarget[0], $sourceAndTarget[1])) {
                    $entries[$sourceAndTarget[0]] = $sourceAndTarget[1];
                }
            }
        }

        return $entries;
    }
}
