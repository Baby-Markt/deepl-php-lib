<?php

namespace BabyMarkt\DeepL;

/**
 * ClientInterface encapsules the functionality relevant
 */
interface ClientInterface
{
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
    public function request($url, $body = '', $method = 'POST');

    /**
     * Set a proxy to use for querying the DeepL API if needed
     *
     * @param string $proxy Proxy URL (e.g 'http://proxy-domain.com:3128')
     */
    public function setProxy($proxy);

    /**
     * Set the proxy credentials
     *
     * @param string $proxyCredentials proxy credentials (using 'username:password' format)
     */
    public function setProxyCredentials($proxyCredentials);

    /**
     * Set a timeout for queries to the DeepL API
     *
     * @param int $timeout Timeout in seconds
     */
    public function setTimeout($timeout);

    /**
     * Creates the Base-Url which all of the 3 API-resources have in common.
     *
     * @param string $resource
     * @param bool   $withAuth
     *
     * @return string
     */
    public function buildBaseUrl(string $resource = 'translate'): string;

    /**
     * @param array $paramsArray
     *
     * @return string
     */
    public function buildQuery($paramsArray);
}
