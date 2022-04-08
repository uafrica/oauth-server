<?php

namespace OAuthServer\Bridge\Entity;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

trait TokenEntityTrait
{

    /**
     * {@inheritDoc}
     */
    public function getUserIdentifier()
    {
        return $this->user_id;
    }

    /**
     * {@inheritDoc}
     */
    public function setUserIdentifier($identifier)
    {
        $this->user_id = $identifier;
    }

    /**
     * {@inheritDoc}
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * {@inheritDoc}
     */
    public function setClient(ClientEntityInterface $client)
    {
        $this->client = $client;
        $this->client_id = $client->getIdentifier();
    }

    /**
     * {@inheritDoc}
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * {@inheritDoc}
     */
    public function addScope(ScopeEntityInterface $scope)
    {
        if ($this->scopes === null) {
            $this->scopes = [];
        }
        $this->scopes[] = $scope;
    }
}
