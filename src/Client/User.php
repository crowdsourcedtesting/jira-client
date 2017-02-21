<?php

namespace CST\Jira\Client;

class User extends AbstractClient {

    /**
     * Returns a user
     * @param  array $params Query string params
     * @return array
     */
    public function getUser(array $params = []) {
        return $this->get('user', $params)->json();
    }

}
