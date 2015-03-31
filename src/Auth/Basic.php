<?php

namespace CST\Jira\Auth;

use GuzzleHttp\Message\Request as GuzzleRequest;

class Basic implements AuthInterface {

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public function authorize(GuzzleRequest $request) {
        $encoded = 'Basic ' . base64_encode( implode(':', [$this->username, $this->password]) );
        $request->addHeader('Authorization', $encoded);
        return $request;
    }

}