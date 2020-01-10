<?php

namespace OAuthServer\Model\Storage;

use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\ScopeInterface;

class ScopeStorage extends AbstractStorage implements ScopeInterface
{
    /**
     * {@inheritdoc}
     *
     * @param string $scope Scope
     * @param null|string $grantType Type of grant
     * @param null|string $clientId Client
     * @return \League\OAuth2\Server\Entity\EntityTrait
     */
    public function get($scope, $grantType = null, $clientId = null)
    {
        $this->loadModel('OAuthServer.Scopes');
        $result = $this->Scopes->find()
            ->where([
                'id' => $scope,
            ])
            ->first();

        if ($result) {
            return (new ScopeEntity($this->server))->hydrate([
                    'id' => $result->id,
                    'description' => $result->description,
                ]);
        }
    }
}
