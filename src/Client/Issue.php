<?php
namespace CST\Jira\Client;

class Issue extends AbstractClient {

    /**
     * Returns a full representation of the issue for the given issue key.
     * @param  string $idOrKey Issue ID or Key
     * @param  array $params Query string params
     * @return array
     */
    public function getIssue($idOrKey, array $params = []) {
        return $this->get("issue/{$idOrKey}", $params)->json();
    }

}