<?php

namespace BabyMarkt\DeepL;

/**
 * DeepL API client library
 *
 * @package BabyMarkt\DeepL
 */
class DeepL
{
    const API_URL_SCHEMA = 'https';
    /**
     * API BASE URL
     * https://api.deepl.com/v2/[resource]?auth_key=[yourAuthKey]
     */
    const API_URL_BASE = '%s://%s/v%s/%s?auth_key=%s';

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
    const API_URL_SPLIT_SENTENCES = 'split_sentences=%s';

    /**
     * API URL: Parameter preserve_formatting
     */
    const API_URL_PRESERVE_FORMATTING = 'preserve_formatting=%s';

    /**
     * API URL: Parameter non_splitting_tags
     */
    const API_URL_NON_SPLITTING_TAGS = 'non_splitting_tags=%s';

    /**
     * API URL: Parameter outline_detection
     */
    const API_URL_OUTLINE_DETECTION = 'outline_detection=%s';

    /**
     * API URL: Parameter splitting_tags
     */
    const API_URL_SPLITTING_TAGS = 'splitting_tags=%s';

    /**
     * Supported translation source languages
     *
     * @var array
     */
    protected $sourceLanguages = array(
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
    );

    /**
     * Supported translation destination languages
     *
     * @var array
     */
    protected $destinationLanguages = array(
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
    );

    /**
     * DeepL API Version (v2 is default since 2018)
     *
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
     * Hostname of the API (in most cases api.deepl.com)
     *
     * @var string
     */
    protected $host;

    /**
     * DeepL constructor
     *
     * @param string  $authKey
     * @param integer $apiVersion
     * @param string  $host
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
     * @param string          $tagHandling
     * @param array|null      $ignoreTags
     * @param string          $formality
     * @param string          $resource
     * @param null            $splitSentences
     * @param null            $preserveFormatting
     * @param array|null      $nonSplittingTags
     * @param null            $outlineDetection
     * @param array|null      $splittingTags
     *
     * @return mixed
     * @throws DeepLException
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function translate(
        $text,
        $sourceLanguage = 'de',
        $destinationLanguage = 'en',
        $tagHandling = null,
        array $ignoreTags = null,
        $formality = "default",
        $resource = self::API_URL_RESOURCE_TRANSLATE,
        $splitSentences = null,
        $preserveFormatting = null,
        array $nonSplittingTags = null,
        $outlineDetection = null,
        array $splittingTags = null
    ) {
        // make sure we only accept supported languages
        $this->checkLanguages($sourceLanguage, $destinationLanguage);

        // build the DeepL API request url
        $url = $this->buildUrl(
            $sourceLanguage,
            $destinationLanguage,
            $tagHandling,
            $ignoreTags,
            $formality,
            $resource,
            $splitSentences,
            $preserveFormatting,
            $nonSplittingTags,
            $outlineDetection,
            $splittingTags
        );

        $body = $this->buildBody($text);

        // request the DeepL API
        $translationsArray = $this->request($url, $body);
        $translationsCount = count($translationsArray['translations']);

        if ($translationsCount === 0) {
            throw new DeepLException('No translations found.');
        } elseif ($translationsCount === 1) {
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
    protected function checkLanguages($sourceLanguage, $destinationLanguage)
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
     * @param string     $sourceLanguage
     * @param string     $destinationLanguage
     * @param string     $tagHandling
     * @param array|null $ignoreTags
     * @param string     $formality
     * @param string     $resource
     * @param null       $splitSentences
     * @param null       $preserveFormatting
     * @param array|null $nonSplittingTags
     * @param null       $outlineDetection
     * @param array|null $splittingTags
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    protected function buildUrl(
        $sourceLanguage = null,
        $destinationLanguage = null,
        $tagHandling = null,
        array $ignoreTags = null,
        $formality = 'default',
        $resource = self::API_URL_RESOURCE_TRANSLATE,
        $splitSentences = null,
        $preserveFormatting = null,
        array $nonSplittingTags = null,
        $outlineDetection = 1,
        array $splittingTags = null
    ) {
        $url = $this->buildBaseUrl($resource);

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
            $url .= '&'.sprintf(self::API_URL_IGNORE_TAGS, urlencode(implode(',', $ignoreTags)));
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
            $url .= '&'.sprintf(self::API_URL_NON_SPLITTING_TAGS, urlencode(implode(',', $nonSplittingTags)));
        }

        if (0 === $outlineDetection) {
            $url .= '&'.sprintf(self::API_URL_OUTLINE_DETECTION, $outlineDetection);
        }

        if (false === empty($splittingTags)) {
            $url .= '&'.sprintf(self::API_URL_SPLITTING_TAGS, urlencode(implode(',', $splittingTags)));
        }

        return $url;
    }

    /**
     * Creates the Base-Url which all of the 3 API-recourses have in common.
     *
     * @param string $resource
     *
     * @return string
     */
    protected function buildBaseUrl($resource = 'translate')
    {
        $url = sprintf(
            self::API_URL_BASE,
            self::API_URL_SCHEMA,
            $this->host,
            $this->apiVersion,
            $resource,
            $this->authKey
        );

        return $url;
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
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

        $response = curl_exec($this->curl);

        if (curl_errno($this->curl)) {
            throw new DeepLException('There was a cURL Request Error.');
        }
        $httpCode      = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $responseArray = json_decode($response, true);

        if ($httpCode != 200) {
            throw new DeepLException($responseArray['message'], $httpCode);
        }

        if (false === is_array($responseArray)) {
            throw new DeepLException('The Response seems to not be valid JSON.');
        }

        return $responseArray;
    }

    /**
     * Calls the usage-Endpoint and return Json-response as an array
     *
     * @return array
     * @throws DeepLException
     */
    public function usage()
    {
        $body  = '';
        $url   = $this->buildBaseUrl(self::API_URL_RESOURCE_USAGE);
        $usage = $this->request($url, $body);

        return $usage;
    }

    /**
     * Call languages-Endpoint and return Json-response as an Array
     *
     * @return array
     * @throws DeepLException
     */
    public function languages()
    {
        $body      = '';
        $url       = $this->buildBaseUrl(self::API_URL_RESOURCE_LANGUAGES);
        $languages = $this->request($url, $body);

        return $languages;
    }
}
