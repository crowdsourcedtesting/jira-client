<?php

namespace CST\Jira\Client;

class Groups extends AbstractClient {

    /**
     * Returns groups with substrings matching a given query.
     * @param  array $params Query string params
     * @return array
     */
    public function getPicker(array $params = []) {
        return $this->get('groups/picker', $params)->json();
    }

}
