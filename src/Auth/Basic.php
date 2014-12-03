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
        return $request->setAuth($this->username, $this->password);
    }

}