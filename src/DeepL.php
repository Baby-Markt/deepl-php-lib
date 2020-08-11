<?php

namespace BabyMarkt\DeepL;

/**
 * DeepL API client library
 *
 * @package BabyMarkt\DeepL
 */
class DeepL
{
    /**
     * API BASE URL
     */
    const API_URL_BASE = '%s://%s/v%s';

    /**
     * API URL: translate
     */
    const API_URL_RESOURCE_TRANSLATE = 'translate';

    /**
     * API URL: usage
     */
    const API_URL_RESOURCE_USAGE = 'usage';

    /**
     * API URL: languages
     */
    const API_URL_RESOURCE_LANGUAGES = 'languages';

    /**
     * API URL: Parameter auth_key
     */
    const API_URL_AUTH_KEY = 'auth_key=%s';

    /**
     * API URL: Parameter text
     */
    const API_URL_TEXT = 'text=%s';

    /**
     * API URL: Parameter source_lang
     */
    const API_URL_SOURCE_LANG = 'source_lang=%s';

    /**
     * API URL: Parameter target_lang
     */
    const API_URL_DESTINATION_LANG = 'target_lang=%s';

    /**
     * API URL: Parameter tag_handling
     */
    const API_URL_TAG_HANDLING = 'tag_handling=%s';

    /**
     * API URL: Parameter ignore_tags
     */
    const API_URL_IGNORE_TAGS = 'ignore_tags=%s';

    /**
     * API URL: Parameter formality
     */
    const API_URL_FORMALITY = 'formality=%s';

    /**
     * API URL: Parameter split_sentences
     */
    const API_URL_SPLIT_SENTENCES= 'split_sentences=%s';

    /**
     * API URL: Parameter preserve_formatting
     */
    const API_URL_PRESERVE_FORMATTING = 'preserve_formatting=%s';

    /**
     * API URL: Parameter non_splitting_tags
     */
    const API_URL_NON_SPLITTING_TAGS= 'non_splitting_tags=%s';

    /**
     * API URL: Parameter outline_detection
     */
    const API_URL_OUTLINE_DETECTION= 'outline_detection=%s';

    /**
     * API URL: Parameter splitting_tags
     */
    const API_URL_SPLITTING_TAGS = 'splitting_tags=%s';

    /**
     * DeepL HTTP error codes
     *
     * @var array
     */
    protected $errorCodes = [
        400 => 'Wrong request, please check error message and your parameters.',
        403 => 'Authorization failed. Please supply a valid auth_key parameter.',
        413 => 'Request Entity Too Large. The request size exceeds the current limit.',
        429 => 'Too many requests. Please wait and send your request once again.',
        456 => 'Quota exceeded. The character limit has been reached.',
    ];

    /**
     * Supported translation source languages
     *
     * @var array
     */
    protected $sourceLanguages = [
        'EN',
        'DE',
        'FR',
        'ES',
        'PT',
        'IT',
        'NL',
        'PL',
        'RU',
        'JA',
        'ZH',
    ];

    /**
     * Supported translation destination languages
     *
     * @var array
     */
    protected $destinationLanguages = [
        'EN',
        'DE',
        'FR',
        'ES',
        'PT',
        'PT-PT',
        'PT-BR',
        'IT',
        'NL',
        'PL',
        'RU',
        'JA',
        'ZH',
    ];

    /**
     * @var integer
     */
    protected $apiVersion;

    /**
     * DeepL API Auth Key (DeepL Pro access required)
     *
     * @var string
     */
    protected $authKey;

    /**
     * cURL resource
     *
     * @var resource
     */
    protected $curl;

    /**
     * DeepL constructor
     *
     * @param string  $authKey
     * @param integer $apiVersion
     */
    public function __construct($authKey, $apiVersion = 2, $host = 'api.deepl.com')
    {
        $this->authKey    = $authKey;
        $this->apiVersion = $apiVersion;
        $this->host       = $host;
        $this->curl       = curl_init();

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
    }

    /**
     * DeepL destructor
     */
    public function __destruct()
    {
        if ($this->curl && is_resource($this->curl)) {
            curl_close($this->curl);
        }
    }

    /**
     * Translate the text string or array from source to destination language
     *
     * @param string|string[] $text
     * @param string          $sourceLanguage
     * @param string          $destinationLanguage
     * @param array           $tagHandling
     * @param array           $ignoreTags
     * @param string          $formality
     *
     * @return string|string[]
     *
     * @throws DeepLException
     */
    public function translate(
        string $text,
        string $sourceLanguage = 'de',
        string $destinationLanguage = 'en',
        string $tagHandling = null,
        array $ignoreTags = null,
        string $formality = "default",
        string $resource = 'translate',
        string $splitSentences = null,
        bool $preserveFormatting = null,
        array $nonSplittingTags = null,
        bool $outlineDetection = null,
        array $splittingTags = null
    ) {
        // make sure we only accept supported languages
        $this->checkLanguages($sourceLanguage, $destinationLanguage);

        // build the DeepL API request url
        $url  = $this->buildUrl(
            $sourceLanguage,
            $destinationLanguage,
            $tagHandling,
            $ignoreTags,
            $formality,
            self::API_URL_RESOURCE_TRANSLATE
        );
        $body = $this->buildBody($text);

        // request the DeepL API
        $translationsArray = $this->request($url, $body);
        $translationsCount = count($translationsArray['translations']);

        if ($translationsCount == 0) {
            throw new DeepLException('No translations found.');
        } elseif ($translationsCount == 1) {
            return $translationsArray['translations'][0]['text'];
        }

        return $translationsArray['translations'];
    }

    /**
     * Check if the given languages are supported
     *
     * @param string $sourceLanguage
     * @param string $destinationLanguage
     *
     * @return boolean
     *
     * @throws DeepLException
     */
    protected function checkLanguages(string $sourceLanguage, string $destinationLanguage)
    {
        $sourceLanguage = strtoupper($sourceLanguage);

        if (false === in_array($sourceLanguage, $this->sourceLanguages)) {
            throw new DeepLException(
                sprintf('The language "%s" is not supported as source language.', $sourceLanguage)
            );
        }

        $destinationLanguage = strtoupper($destinationLanguage);

        if (false === in_array($destinationLanguage, $this->destinationLanguages)) {
            throw new DeepLException(
                sprintf('The language "%s" is not supported as destination language.', $destinationLanguage)
            );
        }

        return true;
    }

    /**
     * Build the URL for the DeepL API request
     *
     * @param string $sourceLanguage
     * @param string $destinationLanguage
     * @param array  $tagHandling
     * @param array  $ignoreTags
     * @param string $formality
     * @param string $resource
     *
     * @return string
     */
    protected function buildUrl(
        string $sourceLanguage = null,
        string $destinationLanguage = null,
        string $tagHandling = null,
        array $ignoreTags = null,
        string $formality = 'default',
        string $resource = 'translate',
        string $splitSentences = null,
        bool $preserveFormatting = null,
        array $nonSplittingTags = null,
        bool $outlineDetection = null,
        array $splittingTags = null
    ) {
        $url = sprintf(self::API_URL_BASE, 'https', $this->host, $this->apiVersion);
        $url .= sprintf('/%s', $resource);
        $url .= '?'.sprintf(self::API_URL_AUTH_KEY, $this->authKey);

        if (false === empty($sourceLanguage)) {
            $url .= '&'.sprintf(self::API_URL_SOURCE_LANG, strtolower($sourceLanguage));
        }

        if (false === empty($destinationLanguage)) {
            $url .= '&'.sprintf(self::API_URL_DESTINATION_LANG, strtolower($destinationLanguage));
        }

        if (false === empty($tagHandling)) {
            $url .= '&'.sprintf(self::API_URL_TAG_HANDLING, $tagHandling);
        }

        if (false === empty($ignoreTags)) {
            $url .= '&'.sprintf(self::API_URL_IGNORE_TAGS, implode(',', $ignoreTags));
        }

        if (false === empty($formality)) {
            $url .= '&'.sprintf(self::API_URL_FORMALITY, $formality);
        }

        if (false === empty($splitSentences)) {
            $url .= '&'.sprintf(self::API_URL_SPLIT_SENTENCES, $splitSentences);
        }

        if (false === empty($preserveFormatting)) {
            $url .= '&'.sprintf(self::API_URL_PRESERVE_FORMATTING, $preserveFormatting);
        }

        if (false === empty($nonSplittingTags)) {
            $url .= '&'.sprintf(self::API_URL_NON_SPLITTING_TAGS, implode(',', $nonSplittingTags));
        }

        if (false === empty($outlineDetection)) {
            $url .= '&'.sprintf(self::API_URL_OUTLINE_DETECTION, $outlineDetection);
        }

        if (false === empty($splittingTags)) {
            $url .= '&'.sprintf(self::API_URL_SPLITTING_TAGS, implode(',', $splittingTags));
        }

        return $url;
    }

    /**
     * Make a request to the given URL
     *
     * @param string $url
     *
     * @return array
     *
     * @throws DeepLException
     */
    protected function request($url, $body)
    {
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $response = curl_exec($this->curl);

        if (curl_errno($this->curl)) {
            throw new DeepLException('There was a cURL Request Error.');
        }

        $httpCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        if ($httpCode != 200 && array_key_exists($httpCode, $this->errorCodes)) {
            throw new DeepLException($this->errorCodes[$httpCode], $httpCode);
        }

        $translationsArray = json_decode($response, true);

        if (!$translationsArray) {
            throw new DeepLException('The Response seems to not be valid JSON.');
        }

        return $translationsArray;
    }

    /**
     * Build the body for the DeepL API request
     *
     * @param string|string[] $text
     *
     * @return string
     */
    protected function buildBody($text)
    {
        $body  = '';
        $first = true;

        if (!is_array($text)) {
            $text = (array)$text;
        }

        foreach ($text as $textElement) {
            $body .= ($first ? '' : '&').sprintf(self::API_URL_TEXT, rawurlencode($textElement));

            if ($first) {
                $first = false;
            }
        }

        return $body;
    }

    /**
     * @return array
     * @throws DeepLException
     */
    public function usage()
    {
        $result = [];
        $body   = '';
        $url    = $this->buildUrl(null, null, null, null, '', self::API_URL_RESOURCE_USAGE);
        $result = $this->request($url, $body);

        return $result;
    }


    /**
     * @return array
     * @throws DeepLException
     */
    public function languages()
    {
        $result = [];
        $body   = '';
        $url    = $this->buildUrl(null, null, [], [], '', self::API_URL_RESOURCE_LANGUAGES);
        $result = $this->request($url, $body);

        return $result;
    }
}
