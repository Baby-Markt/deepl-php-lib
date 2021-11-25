<?php

namespace BabyMarkt\DeepL;

use InvalidArgumentException;

/**
 * DeepL API client library
 *
 * @package BabyMarkt\DeepL
 */
class DeepL
{
    /**
     * API URL: usage
     */
    const API_URL_RESOURCE_USAGE = 'usage';

    /**
     * API URL: languages
     */
    const API_URL_RESOURCE_LANGUAGES = 'languages';

    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct($authKey, $apiVersion = 2, $host = 'api.deepl.com', ClientInterface $client = null)
    {
        $this->client = $client ?? new Client($authKey, $apiVersion, $host);
    }

    /**
     * Call languages-Endpoint and return Json-response as an Array
     *
     * @param string $type
     *
     * @return array
     *
     * @throws DeepLException
     */
    public function languages($type = null)
    {
        $url       = $this->client->buildBaseUrl(self::API_URL_RESOURCE_LANGUAGES);
        $body      = $this->client->buildQuery(array('type' => $type));
        $languages = $this->client->request($url, $body);

        return $languages;
    }

    /**
     * Translate the text string or array from source to destination language
     * For detailed info on Parameters see README.md
     *
     * @param string|string[] $text
     * @param string          $sourceLang
     * @param string          $targetLang
     * @param string          $tagHandling
     * @param array|null      $ignoreTags
     * @param string          $formality
     * @param null            $splitSentences
     * @param null            $preserveFormatting
     * @param array|null      $nonSplittingTags
     * @param null            $outlineDetection
     * @param array|null      $splittingTags
     *
     * @return array
     *
     * @throws DeepLException
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function translate(
        $text,
        $sourceLang = 'de',
        $targetLang = 'en',
        $tagHandling = null,
        array $ignoreTags = null,
        $formality = 'default',
        $splitSentences = null,
        $preserveFormatting = null,
        array $nonSplittingTags = null,
        $outlineDetection = null,
        array $splittingTags = null
    ) {
        if (is_array($tagHandling)) {
            throw new InvalidArgumentException('$tagHandling must be of type String in V2 of DeepLLibrary');
        }
        $paramsArray = array(
            'text'                => $text,
            'source_lang'         => $sourceLang,
            'target_lang'         => $targetLang,
            'splitting_tags'      => $splittingTags,
            'non_splitting_tags'  => $nonSplittingTags,
            'ignore_tags'         => $ignoreTags,
            'tag_handling'        => $tagHandling,
            'formality'           => $formality,
            'split_sentences'     => $splitSentences,
            'preserve_formatting' => $preserveFormatting,
            'outline_detection'   => $outlineDetection,
        );

        $paramsArray = $this->removeEmptyParams($paramsArray);
        $url         = $this->client->buildBaseUrl();
        $body        = $this->client->buildQuery($paramsArray);

        // request the DeepL API
        $translationsArray = $this->client->request($url, $body);

        return $translationsArray['translations'];
    }

    /**
     * Calls the usage-Endpoint and return Json-response as an array
     *
     * @return array
     *
     * @throws DeepLException
     */
    public function usage()
    {
        $url   = $this->client->buildBaseUrl(self::API_URL_RESOURCE_USAGE);
        $usage = $this->client->request($url);

        return $usage;
    }

    /**
     * @param array $paramsArray
     *
     * @return array
     */
    private function removeEmptyParams($paramsArray)
    {
        foreach ($paramsArray as $key => $value) {
            if (true === empty($value)) {
                unset($paramsArray[$key]);
            }
            // Special Workaround for outline_detection which will be unset above
            // DeepL assumes outline_detection=1 if it is not send
            // in order to deactivate it, we need to send outline_detection=0 to the api
            if ('outline_detection' === $key) {
                if (1 === $value) {
                    unset($paramsArray[$key]);
                }

                if (0 === $value) {
                    $paramsArray[$key] = 0;
                }
            }
        }

        return $paramsArray;
    }
}
