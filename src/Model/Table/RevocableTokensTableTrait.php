<?php

namespace OAuthServer\Model\Table;

use Cake\I18n\FrozenTime;
use Cake\ORM\Query;

trait RevocableTokensTableTrait
{
    /**
     * find expired token
     *
     * @param Query $query the query
     * @return Query
     */
    public function findExpired(Query $query): Query
    {
        return $query->where([
            $this->aliasField('expires <') => FrozenTime::now()->getTimestamp(),
        ]);
    }

    /**
     * find revoked token
     *
     * @param Query $query the query
     * @return Query
     */
    public function findRevoked(Query $query): Query
    {
        return $query->where([$this->aliasField('revoked') => true]);
    }
}
