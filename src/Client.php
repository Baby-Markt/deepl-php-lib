<?php

namespace BabyMarkt\DeepL;

/**
 * Class Client implements a DeepL http Client based on PHP-CURL
 */
final class Client implements ClientInterface
{
    const API_URL_SCHEMA = 'https';

    /**
     * API BASE URL without authentication query parameter
     * https://api.deepl.com/v2/[resource]
     */
    const API_URL_BASE_NO_AUTH = '%s://%s/v%s/%s';

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
     * Maximum number of seconds the query should take
     *
     * @var int|null
     */
    protected $timeout = null;

    /**
     * URL of the proxy used to connect to DeepL (if needed)
     *
     * @var string|null
     */
    protected $proxy = null;

    /**
     * Credentials for the proxy used to connect to DeepL (username:password)
     *
     * @var string|null
     */
    protected $proxyCredentials = null;

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
     * Make a request to the given URL
     *
     * @param string $url
     * @param string $body
     * @param string $method
     *
     * @return array
     *
     * @throws DeepLException
     */
    public function request($url, $body = '', $method = 'POST')
    {
        switch ($method) {
            case 'DELETE':
                curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case 'POST':
                curl_setopt($this->curl, CURLOPT_POST, true);
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $body);
                break;
            case 'GET':
            default:
                break;
        }

        curl_setopt($this->curl, CURLOPT_URL, $url);

        curl_setopt($this->curl, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, ['Authorization: DeepL-Auth-Key ' . $this->authKey]);

        if ($this->proxy !== null) {
            curl_setopt($this->curl, CURLOPT_PROXY, $this->proxy);
        }

        if ($this->proxyCredentials !== null) {
            curl_setopt($this->curl, CURLOPT_PROXYAUTH, $this->proxyCredentials);
        }

        if ($this->timeout !== null) {
            curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeout);
        }

        $response = curl_exec($this->curl);

        if (curl_errno($this->curl)) {
            throw new DeepLException('There was a cURL Request Error : ' . curl_error($this->curl));
        }
        $httpCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        return $this->handleResponse($response, $httpCode);
    }

    /**
     * Set a proxy to use for querying the DeepL API if needed
     *
     * @param string $proxy Proxy URL (e.g 'http://proxy-domain.com:3128')
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * Set the proxy credentials
     *
     * @param string $proxyCredentials proxy credentials (using 'username:password' format)
     */
    public function setProxyCredentials($proxyCredentials)
    {
        $this->proxyCredentials = $proxyCredentials;
    }

    /**
     * Set a timeout for queries to the DeepL API
     *
     * @param int $timeout Timeout in seconds
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * Creates the Base-Url which all the API-resources have in common.
     *
     * @param string $resource
     * @param bool   $withAuth
     *
     * @return string
     */
    public function buildBaseUrl(string $resource = 'translate'): string
    {
        return sprintf(
            self::API_URL_BASE_NO_AUTH,
            self::API_URL_SCHEMA,
            $this->host,
            $this->apiVersion,
            $resource
        );
    }

    /**
     * @param array $paramsArray
     *
     * @return string
     */
    public function buildQuery($paramsArray)
    {
        if (isset($paramsArray['text']) && true === is_array($paramsArray['text'])) {
            $text = $paramsArray['text'];
            unset($paramsArray['text']);
            $textString = '';
            foreach ($text as $textElement) {
                $textString .= '&text=' . rawurlencode($textElement);
            }
        }

        foreach ($paramsArray as $key => $value) {
            if (true === is_array($value)) {
                $paramsArray[$key] = implode(',', $value);
            }
        }

        $body = http_build_query($paramsArray, null, '&');

        if (isset($textString)) {
            $body = $textString . '&' . $body;
        }

        return $body;
    }

    /**
     * Handles the different kind of response returned from API, array, string or null
     *
     * @param $response
     * @param $httpCode
     *
     * @return array|mixed|null
     *
     * @throws DeepLException
     */
    private function handleResponse($response, $httpCode)
    {
        $responseArray = json_decode($response, true);
        if (($httpCode === 200 || $httpCode === 204) && is_null($responseArray)) {
            return empty($response) ? null : $response;
        }

        if ($httpCode !== 200 && is_array($responseArray) && array_key_exists('message', $responseArray)) {
            throw new DeepLException($responseArray['message'], $httpCode);
        }

        if (!is_array($responseArray)) {
            throw new DeepLException('The Response seems to not be valid JSON.', $httpCode);
        }

        return $responseArray;
    }
}
