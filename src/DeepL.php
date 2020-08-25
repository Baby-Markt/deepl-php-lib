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
        $formality = 'default',
        $resource = self::API_URL_RESOURCE_TRANSLATE,
        $splitSentences = null,
        $preserveFormatting = null,
        array $nonSplittingTags = null,
        $outlineDetection = null,
        array $splittingTags = null
    ) {
        // make sure we only accept supported languages
        $this->checkLanguages($sourceLanguage, $destinationLanguage);

        $url  = $this->buildBaseUrl($resource);
        $body = $this->buildQuery(
            $text,
            $destinationLanguage,
            $sourceLanguage,
            $splittingTags,
            $nonSplittingTags,
            $ignoreTags,
            $tagHandling,
            $formality,
            $splitSentences,
            $preserveFormatting,
            $outlineDetection
        );

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
     * Creates the Base-Url which all of the 3 API-resources have in common.
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
     * @param $text
     * @param $destinationLanguage
     * @param $sourceLanguage
     * @param $splittingTags
     * @param $nonSplittingTags
     * @param $ignoreTags
     * @param $tagHandling
     * @param $formality
     * @param $splitSentences
     * @param $preserveFormatting
     * @param $outlineDetection
     *
     * @return string
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function buildQuery(
        $text,
        $destinationLanguage,
        $sourceLanguage,
        $splittingTags,
        $nonSplittingTags,
        $ignoreTags,
        $tagHandling,
        $formality,
        $splitSentences,
        $preserveFormatting,
        $outlineDetection
    ) {
        $paramsArray      = array(
            'text'                => $text,
            'source_lang'         => $sourceLanguage,
            'target_lang'         => $destinationLanguage,
            'splitting_tags'      => $splittingTags,
            'non_splitting_tags'  => $nonSplittingTags,
            'ignore_tags'         => $ignoreTags,
            'tag_handling'        => $tagHandling,
            'formality'           => $formality,
            'split_sentences'     => $splitSentences,
            'preserve_formatting' => $preserveFormatting,
            'outline_detection'   => $outlineDetection
        );

        foreach ($paramsArray as $key => $value) {
            if (true === is_array($value) && array() != $value) {
                $paramsArray[$key] = implode(',', $value);
            }

            if (true === empty($value) || ('text' === $key && true === is_array($value))) {
                unset($paramsArray[$key]);
            }

            if ('outline_detection' === $key) {
                $paramsArray[$key] = ('0' != $value) ? null : '0';
            }
        }

        $body = http_build_query($paramsArray, null, '&', PHP_QUERY_RFC3986);

        if (true === is_array($text)) {
            $textString ='';
            foreach ($text as $textElement) {
                $textString .= '&text='.rawurlencode($textElement);
            }
            $body = $textString.'&'.$body;
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

        if ($httpCode != 200 && is_array($responseArray) && array_key_exists('message', $responseArray)) {
            throw new DeepLException($responseArray['message'], $httpCode);
        }

        if (false === is_array($responseArray)) {
            throw new DeepLException('The Response seems to not be valid JSON.', $httpCode);
        }

        return $responseArray;
    }
}
