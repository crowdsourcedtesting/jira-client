<?php

namespace CST\Jira\Client;

class Priority extends AbstractClient {

    /**
     * Returns a list of all issue priorities
     * @param  array $params Query string params
     * @return array
     */
    public function getPriority(array $params = []) {
        return $this->get("priority", $params)->json();
    }

}
