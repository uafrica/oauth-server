<?php

namespace OAuthServer\Model\Table;

use Cake\ORM\Query;

interface RevocableTokensTableInterface
{
    /**
     * find expired token
     *
     * @param Query $query the query
     * @return Query
     */
    public function findExpired(Query $query): Query;

    /**
     * find revoked token
     *
     * @param Query $query the query
     * @return Query
     */
    public function findRevoked(Query $query): Query;
}
