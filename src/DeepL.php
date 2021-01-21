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
     * API URL: usage
     */
    const API_URL_RESOURCE_USAGE = 'usage';

    /**
     * API URL: languages
     */
    const API_URL_RESOURCE_LANGUAGES = 'languages';

    /**
     * DeepL API Version (v2 is default since 2018)
     */
    protected int $apiVersion;

    /**
     * DeepL API Auth Key (DeepL Pro access required)
     */
    protected string $authKey;

    /**
     * cURL resource
     */
    protected $curl;

    /**
     * Hostname of the API (in most cases api.deepl.com)
     */
    protected string $host;

    /**
     * DeepL constructor
     *
     * @param string $authKey
     * @param integer $apiVersion
     * @param string $host
     */
    public function __construct(string $authKey, $apiVersion = 2, $host = 'api.deepl.com')
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
     * Call languages-Endpoint and return Json-response as an Array
     *
     * @param null $type
     *
     * @return array
     * @throws DeepLException
     */
    public function languages($type = null)
    {
        $url       = $this->buildBaseUrl(self::API_URL_RESOURCE_LANGUAGES);
        $body      = $this->buildQuery(array('type' => $type));

        return $this->request($url, $body);
    }

    /**
     * Translate the text string or array from source to destination language
     * For detailed info on Parameters see README.md
     *
     * @param string|string[] $text
     * @param string $sourceLang
     * @param string $targetLang
     * @param null $tagHandling
     * @param array|null $ignoreTags
     * @param string $formality
     * @param null $splitSentences
     * @param null $preserveFormatting
     * @param array|null $nonSplittingTags
     * @param null $outlineDetection
     * @param array|null $splittingTags
     *
     * @return array
     * @throws DeepLException
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function translate(
        string|array $text,
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
            throw new \InvalidArgumentException('$tagHandling must be of type String in V2 of DeepLLibrary');
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
        $url         = $this->buildBaseUrl();
        $body        = $this->buildQuery($paramsArray);

        // request the DeepL API
        $translationsArray = $this->request($url, $body);

        return $translationsArray['translations'];
    }

    /**
     * Calls the usage-Endpoint and return Json-response as an array
     *
     * @return array
     * @throws DeepLException
     */
    public function usage()
    {
        $url   = $this->buildBaseUrl(self::API_URL_RESOURCE_USAGE);

        return $this->request($url);
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
        return sprintf(
            self::API_URL_BASE,
            self::API_URL_SCHEMA,
            $this->host,
            $this->apiVersion,
            $resource,
            $this->authKey
        );
    }

    /**
     * @param array $paramsArray
     *
     * @return string
     */
    protected function buildQuery(array $paramsArray)
    {
        if (isset($paramsArray['text']) && true === is_array($paramsArray['text'])) {
            $text = $paramsArray['text'];
            unset($paramsArray['text']);
            $textString = '';
            foreach ($text as $textElement) {
                $textString .= '&text='.rawurlencode($textElement);
            }
        }

        foreach ($paramsArray as $key => $value) {
            if (true === is_array($value)) {
                $paramsArray[$key] = implode(',', $value);
            }
        }

        $body = http_build_query($paramsArray, null, '&');

        if (isset($textString)) {
            $body = $textString.'&'.$body;
        }

        return $body;
    }


    /**
     * Make a request to the given URL
     *
     * @param string $url
     * @param string $body
     *
     * @return array
     *
     * @throws DeepLException
     */
    protected function request(string $url, $body = '')
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

    /**
     * @param array $paramsArray
     *
     * @return array
     */
    private function removeEmptyParams(array $paramsArray)
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
