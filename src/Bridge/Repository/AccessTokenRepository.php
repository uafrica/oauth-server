<?php

namespace OAuthServer\Bridge\Repository;

use Cake\Datasource\ModelAwareTrait;
use Cake\ORM\Table;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use OAuthServer\Model\Entity\AccessToken;
use OAuthServer\Model\Table\AccessTokensTable;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    use ModelAwareTrait;
    use RevokeTokenRepositoryTrait;

    /**
     * @var AccessTokensTable
     */
    private $table;

    /**
     * RefreshTokenRepository constructor.
     */
    public function __construct()
    {
        $this->table = $this->loadModel('OAuthServer.AccessTokens');
    }

    /**
     * {@inheritDoc}
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $token = $this->table->newEntity([]);

        $token->setClient($clientEntity);
        $token->setUserIdentifier($userIdentifier);
        $token->scopes = $scopes;

        return $token;
    }

    /**
     * {@inheritDoc}
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        if ($this->table->exists([$this->table->getPrimaryKey() => $accessTokenEntity->getIdentifier()])) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        if (!$accessTokenEntity instanceof AccessToken) {
            $data = [
                'oauth_token' => $accessTokenEntity->getIdentifier(),
                'user_id' => $accessTokenEntity->getUserIdentifier(),
                'client_id' => $accessTokenEntity->getClient()->getIdentifier(),
                'expires' => $accessTokenEntity->getExpiryDateTime()->getTimestamp(),
                'scopes' => [],
            ];
            foreach ($accessTokenEntity->getScopes() as $scope) {
                $data['scopes'][] = ['id' => $scope->getIdentifier()];
            }
            $accessTokenEntity = $this->table->newEntity($data);
        }

        if (!$this->table->save($accessTokenEntity)) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function revokeAccessToken($tokenId)
    {
        if ($this->isAccessTokenRevoked($tokenId)) {
            return;
        }

        $this->revokeToken($tokenId);
    }

    /**
     * {@inheritDoc}
     */
    public function isAccessTokenRevoked($tokenId): bool
    {
        return $this->isTokenRevoked($tokenId);
    }

    /**
     * @return string
     */
    protected function getDeleteRecordOnRevokeKeyName(): string
    {
        return 'OAuthServer.deleteAccessTokenOnRevoke';
    }

    /**
     * @inheritDoc
     */
    protected function getTable(): Table
    {
        return $this->table;
    }
}
