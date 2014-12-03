<?php
namespace CST\Jira\Client;

class Search extends AbstractClient {

    /**
     * Returns groups with substrings matching a given query.
     * @param  array $params Query string params
     * @return array
     */
    public function getSearch(array $params = []) {
        return $this->get('search', $params)->json();
    }

}