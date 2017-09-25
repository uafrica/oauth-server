<?php

namespace OAuthServer\Model\Storage;

use Cake\ORM\Entity;
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\AuthCodeEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\SessionInterface;

class SessionStorage extends AbstractStorage implements SessionInterface
{
    /**
     * {@inheritdoc}
     *
     * @param \League\OAuth2\Server\Entity\AccessTokenEntity $accessToken Access token
     * @return \League\OAuth2\Server\Entity\SessionEntity
     */
    public function getByAccessToken(AccessTokenEntity $accessToken)
    {
        $this->loadModel('OAuthServer.Sessions');
        $result = $this->Sessions->find()
            ->matching('AccessTokens', function ($q) use ($accessToken) {
                return $q->where([
                            'oauth_token' => $accessToken->getId()
                        ]);
            })
            ->first();

        if ($result) {
            $session = new SessionEntity($this->server);
            $session->setId($result->id);
            $session->setOwner($result->owner_model, $result->owner_id);

            return $session;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param \League\OAuth2\Server\Entity\AuthCodeEntity $authCode Auth code
     * @return \League\OAuth2\Server\Entity\SessionEntity
     */
    public function getByAuthCode(AuthCodeEntity $authCode)
    {
        $this->loadModel('OAuthServer.Sessions');
        $result = $this->Sessions->find()
            ->matching('AuthCodes', function ($q) use ($authCode) {
                return $q->where([
                            'code' => $authCode->getId()
                        ]);
            })
            ->first();

        if ($result) {
            $session = new SessionEntity($this->server);
            $session->setId($result->id);
            $session->setOwner($result->owner_model, $result->owner_id);

            return $session;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param \League\OAuth2\Server\Entity\SessionEntity $session Session entity
     * @return array
     */
    public function getScopes(SessionEntity $session)
    {
        $this->loadModel('OAuthServer.SessionScopes');
        $result = $this->SessionScopes->find()
            ->contain([
                    'Scopes'
                ])
            ->where([
                    'session_id' => $session->getId()
                ])
            ->map(function (Entity $scope) {
                return (new ScopeEntity($this->server))->hydrate([
                        'id' => $scope->scope->id,
                        'description' => $scope->scope->description,
                    ]);
            });

        return $result->toArray();
    }

    /**
     * {@inheritdoc}
     *
     * @param string $ownerType Type of owner
     * @param string $ownerId Owner id
     * @param string $clientId Client id
     * @param null|string $clientRedirectUri Redirect uri
     * @return int
     */
    public function create($ownerType, $ownerId, $clientId, $clientRedirectUri = null)
    {
        $this->loadModel('OAuthServer.Sessions');
        $session = $this->Sessions->newEntity([
                'owner_model' => $ownerType,
                'owner_id' => $ownerId,
                'client_id' => $clientId,
            ]);
        $this->Sessions->save($session);

        return $session->id;
    }

    /**
     * {@inheritdoc}
     *
     * @param \League\OAuth2\Server\Entity\SessionEntity $session Session entity
     * @param \League\OAuth2\Server\Entity\ScopeEntity $scope Scope entity
     * @return void
     */
    public function associateScope(SessionEntity $session, ScopeEntity $scope)
    {
        $this->loadModel('OAuthServer.SessionScopes');
        $sessionScope = $this->SessionScopes->newEntity([
                'session_id' => $session->getId(),
                'scope_id' => $scope->getId(),
            ]);
        $this->SessionScopes->save($sessionScope);
    }
}
