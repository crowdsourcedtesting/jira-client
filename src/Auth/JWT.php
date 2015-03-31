<?php

namespace CST\Jira\Auth;

use GuzzleHttp\Message\Request as GuzzleRequest;

class JWT implements AuthInterface {

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var integer
     */
    protected $timestamp;

    /**
     * @var integer
     */
    protected $expiration;

    /**
     * @param string  $key This is the clientKey that you receive in the installed callback
     * @param string  $secret the shared secret key received during the addon installation handshake
     * @param integer $expiration Expiration time. UTC Unix time after which you should no longer accept this token
     */
    public function __construct($key, $secret, $expiration = 300) {
        $this->key = $key;
        $this->secret = $secret;
        $this->expiration = $expiration;
    }

    public function authorize(GuzzleRequest $request) {
        $jwt = $this->generateJWT(
            $request->getUrl(),
            $this->key,
            $this->secret,
            $this->expiration
        );
        $request->setHeader('Authorization', "JWT {$jwt}");
        return $request;
    }

    public function signUrl($url, $key, $secret, $license, $expiration = 900)
    {
        $jwt = $this->generateJWT($url, $key, $secret, $expiration);

        if(stripos($url, '?') === false) {
            return "{$url}?jwt={$jwt}&lic={$license}";
        }
        else {
            return "{$url}&jwt={$jwt}&lic={$license}";
        }
    }

    public function generateJWT($url, $key, $secret, $expiration)
    {
        $payload = array(
            "iss" => $key,
            "qsh" => $this->canonicalizeUrl( $url ),
            "iat" => time(),
            "exp" => time()+$expiration,
        );

        return \JWT::encode($payload, $secret);
    }

    /**
     * Extracted from Symfony\Component\HttpFoundation\Request
     * @param  string $qs
     * @return string
     */
    protected function normalizeQueryString($qs) {
      if ('' == $qs) {
        return '';
      }

      $parts = array();
      $order = array();

      foreach (explode('&', $qs) as $param) {
        if ('' === $param || '=' === $param[0]) {
          // Ignore useless delimiters, e.g. "x=y&".
          // Also ignore pairs with empty key, even if there was a value, e.g. "=value", as such nameless values cannot be retrieved anyway.
          // PHP also does not include them when building _GET.
          continue;
        }

        $keyValuePair = explode('=', $param, 2);

        // GET parameters, that are submitted from a HTML form, encode spaces as "+" by default (as defined in enctype application/x-www-form-urlencoded).
        // PHP also converts "+" to spaces when filling the global _GET or when using the function parse_str. This is why we use urldecode and then normalize to
        // RFC 3986 with rawurlencode.
        $parts[] = isset($keyValuePair[1]) ? rawurlencode(urldecode($keyValuePair[0])) . '=' . rawurlencode(urldecode($keyValuePair[1])) : rawurlencode(urldecode($keyValuePair[0]));
        $order[] = urldecode($keyValuePair[0]);
      }

      array_multisort($order, SORT_ASC, $parts);

      return implode('&', $parts);
    }

    /**
     * Generate custom Atlassian hash that prevents URL tampering.
     * https://developer.atlassian.com/static/connect/docs/concepts/understanding-jwt.html#qsh
     * @param  string $url
     * @return string Hash
     */
    protected function canonicalizeUrl($url) {
        $parsed = parse_url($url);
        $query = isset($parsed['query']) ? $this->normalizeQueryString($parsed['query']) : '';
        $canonicalRequest = "GET&{$parsed['path']}&{$query}";
        return hash('sha256', $canonicalRequest);
    }

}