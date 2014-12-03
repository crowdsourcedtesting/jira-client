<?php

namespace CST\Jira\Client;

use CST\Jira\Auth\AuthInterface;
use GuzzleHttp\Client as GuzzleClient;

use GuzzleHttp\Exception\RequestException;


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
     * @param string $domain Atlassian domain name ([domain].atlassian.net) || Jira installation URL
     * @param string $auth
     * @param string $password
     */
    public function __construct($domain, AuthInterface $authMethod)
    {
        if(!filter_var($domain, FILTER_VALIDATE_URL)) {
            $domain =  sprintf('https://%s.atlassian.net/rest/api/latest/', $domain);
        }
        else {
            $domain = "{$domain}/rest/api/latest/";
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
     * @param  string $query
     * @return \GuzzleHttp\Message\Response
     */
    public function get($uri, $query = [])
    {
        $request = $this->getHttp()->createRequest('GET', $uri, [
            'query' => $query
        ]);

        $request = $this->getAuth()->authorize( $request );
        // try {
            return $this->getHttp()->send($request);
        // }
        // catch (RequestException $e) {
        //     echo $e->getRequest();
        //     if ($e->hasResponse()) {
        //         echo $e->getResponse();
        //     }
        //     die;
        // }

    }

    /**
     * @param  string $uri
     * @param  string $data
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
     * @param  resource $file
     *
     * @return \GuzzleHttp\Message\Response
     */
    public function postFile($uri, $file = null)
    {
        return $this->getClient()->post($uri, [
            'headers' => ['X-Atlassian-Token' => 'no-check'],
            'body' => ['file' => $file]
        ]);
    }

    /**
     * @param  string $uri
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
     * @param  array  $data
     *
     * @return array
     */
    protected function createBody(array $data = null)
    {
        return $data ?: array();
    }
}