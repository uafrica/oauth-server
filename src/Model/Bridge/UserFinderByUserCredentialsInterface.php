<?php

namespace OAuthServer\Model\Bridge;

use Cake\Datasource\EntityInterface;

interface UserFinderByUserCredentialsInterface
{
    /**
     * Find user from repository
     *
     * @param string $username a username
     * @param string $password a password
     * @return EntityInterface|null
     */
    public function findUser($username, $password): ?EntityInterface;

    /**
     * Get Users repository primary key
     *
     * @return string
     */
    public function getPrimaryKey();
}
