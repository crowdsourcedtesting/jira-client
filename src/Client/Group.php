<?php

namespace CST\Jira\Client;

class Group extends AbstractClient {

    /**
     * Returns REST representation for the requested group. Allows to get list of active users belonging to the specified group and its subgroups if "users" expand option is provided.
     * @param  array $params Query string params
     * @return array
     */
    public function getGroup(array $params = []) {
        return $this->get('group', $params)->json();
    }

}
