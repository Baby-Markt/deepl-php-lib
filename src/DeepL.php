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
     * API v1 URL
     */
    const API_URL                  = 'https://api.deepl.com/v1/translate';

    /**
     * API v1  URL: Parameter auth_key
     */
    const API_URL_AUTH_KEY         = 'auth_key=%s';

    /**
     * API v1 URL: Parameter text
     */
    const API_URL_TEXT             = 'text=%s';

    /**
     * API v1 URL: Parameter source_lang
     */
    const API_URL_SOURCE_LANG      = 'source_lang=%s';

    /**
     * API v1 URL: Parameter target_lang
     */
    const API_URL_DESTINATION_LANG = 'target_lang=%s';

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
        'IT',
        'NL',
        'PL'
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
        'IT',
        'NL',
        'PL'
    );

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
     * @param $authKey
     */
    public function __construct($authKey)
    {
        $this->authKey = $authKey;
        $this->curl    = curl_init();

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
    }

    /**
     * DeepL Destructor
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
     * @param $text                string|string[]
     * @param $sourceLanguage      string
     * @param $destinationLanguage string
     *
     * @return array
     *
     * @throws DeepLException
     */
    public function translate($text, $sourceLanguage = 'de', $destinationLanguage = 'en')
    {
        // make sure we only accept supported languages
        $this->checkLanguages($sourceLanguage, $destinationLanguage);

        $url = $this->buildUrl($text, $sourceLanguage, $destinationLanguage);

        return $this->request($url);
    }

    /**
     * Check if the given languages are supported
     *
     * @param $sourceLanguage
     * @param $destinationLanguage
     *
     * @return boolean
     *
     * @throws DeepLException
     */
    protected function checkLanguages($sourceLanguage, $destinationLanguage)
    {
        $sourceLanguage = strtoupper($sourceLanguage);

        if (!in_array($sourceLanguage, $this->sourceLanguages)) {
            throw new DeepLException(sprintf('The language "%s" is not supported as source language.', $sourceLanguage));
        }

        $destinationLanguage = strtoupper($destinationLanguage);

        if (!in_array($destinationLanguage, $this->destinationLanguages)) {
            throw new DeepLException(sprintf('The language "%s" is not supported as destination language.', $sourceLanguage));
        }

        return true;
    }

    /**
     * Build the URL for the DeepL API request
     *
     * @param $text
     * @param $sourceLanguage
     * @param $destinationLanguage
     *
     * @return string
     */
    protected function buildUrl($text, $sourceLanguage, $destinationLanguage)
    {
        if (!is_array($text)) {
            $text = (array)$text;
        }

        $url = DeepL::API_URL . '?' . sprintf(DeepL::API_URL_AUTH_KEY, $this->authKey);

        foreach ($text as $textElement) {
            $url .= '&' . sprintf(DeepL::API_URL_TEXT, rawurlencode($textElement));
        }

        $url .= '&' . sprintf(DeepL::API_URL_SOURCE_LANG, strtolower($sourceLanguage));
        $url .= '&' . sprintf(DeepL::API_URL_DESTINATION_LANG, strtolower($destinationLanguage));

        return $url;
    }

    /**
     * Make a request to the given URL
     *
     * @param $url
     *
     * @return array
     *
     * @throws DeepLException
     */
    protected function request($url)
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        $response = curl_exec($this->curl);

        // if there was a curl error
        if ($response == false) {

            $errorString = curl_error($this->curl);
            $errorNumber = curl_errno($this->curl);

            if (!$errorString) {
                $errorString = 'There was a cURL Response Error - please check your DeepL Auth Key.';
            }

            throw new DeepLException($errorString, $errorNumber);
        }

        $translationsArray = json_decode($response, true);

        return $translationsArray;
    }
}