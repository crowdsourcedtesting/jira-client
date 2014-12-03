<?php
namespace CST\Jira\Auth;

use GuzzleHttp\Message\Request as GuzzleRequest;

interface AuthInterface
{
    public function authorize(GuzzleRequest $request);
}