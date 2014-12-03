<?php
namespace CST\Jira\Client;

use CST\Jira\Auth\AuthInterface;
use GuzzleHttp\Client as GuzzleClient;

use GuzzleHttp\Exception\RequestException;

class License extends AbstractClient {

    public function __construct($domain, AuthInterface $authMethod)
    {
        if(!filter_var($domain, FILTER_VALIDATE_URL)) {
            $domain =  sprintf('https://%s.atlassian.net/rest/atlassian-connect/latest/', $domain);
        }
        else {
            $domain = "{$domain}/rest/atlassian-connect/latest/";
        }
        $this->httpClient = new \GuzzleHttp\Client(['base_url' => $domain]);
        $this->auth = $authMethod;
    }

    /**
     * Returns groups with substrings matching a given query.
     * @param  array $params Query string params
     * @return array
     */
    public function getLicense(array $params = []) {
        return $this->get("license", $params)->json();
    }

}