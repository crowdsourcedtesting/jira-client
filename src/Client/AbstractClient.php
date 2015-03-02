<?php

namespace CST\Jira\Client;

use CST\Jira\Auth\AuthInterface;

abstract class AbstractClient
{
    /**
     * @var GuzzleHttp
     */
    protected $httpClient;

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @param string $domain Atlassian domain name ([domain].atlassian.net) || Jira instance URL
     * @param AuthInterface $authMethod
     */
    public function __construct($domain, AuthInterface $authMethod)
    {
        if (!filter_var($domain, FILTER_VALIDATE_URL)) {
            $domain = sprintf('https://%s.atlassian.net/rest/api/latest/', $domain);
        } else {
            $urlParts = parse_url($domain);

            $domain = implode('', [
                $urlParts['scheme'],
                '://',
                $urlParts['host'],
                '/rest/api/latest/'
            ]);
        }
        $this->httpClient = new \GuzzleHttp\Client(['base_url' => $domain]);
        $this->auth = $authMethod;
    }

    /**
     * @return \GuzzleHttp
     */
    public function getHttp()
    {
        return $this->httpClient;
    }

    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * @param  string $uri
     * @param  array $query
     * @return \GuzzleHttp\Message\Response
     */
    public function get($uri, $query = [])
    {
        $request = $this->getHttp()->createRequest('GET', $uri, [
            'query' => $query
        ]);

        $request = $this->getAuth()->authorize($request);
        return $this->getHttp()->send($request);
    }

    /**
     * @param  string $uri
     * @param  array $data
     *
     * @return \GuzzleHttp\Message\Response
     */
    public function postRequest($uri, array $data = null)
    {
        return $this->getClient()->post($uri, array(
            'json' => $this->createBody($data)
        ));
    }

    /**
     * @param  string $uri
     * @param  resource $fileHandle
     *
     * @return \GuzzleHttp\Message\Response
     */
    public function postFile($uri, $fileHandle = null)
    {
        return $this->getClient()->post($uri, [
            'headers' => ['X-Atlassian-Token' => 'no-check'],
            'body' => ['file' => $fileHandle]
        ]);
    }

    /**
     * @param string $uri
     * @param array $data
     *
     * @return \GuzzleHttp\Message\Response
     */
    public function putRequest($uri, array $data = null)
    {
        return $this->getClient()->put($uri, array(
            'json' => $this->createBody($data)
        ));
    }

    /**
     * @param  string $uri
     *
     * @return GuzzleHttp\Message\Response
     */
    public function deleteRequest($uri)
    {
        return $this->getClient()->delete($uri);
    }

    /**
     * @param  array $data
     *
     * @return array
     */
    protected function createBody(array $data = null)
    {
        return $data ?: array();
    }
}