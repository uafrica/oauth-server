<?php

namespace OAuthServer\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Table;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use OAuthServer\Model\Entity\AccessToken;
use OAuthServer\Lib\Data\Entity\AccessToken as AccessTokenData;
use function Functional\map;

/**
 * OAuth 2.0 access tokens table
 *
 * @property AccessTokenScopesTable|HasMany $AccessTokenScopes
 *
 * @method AccessToken get($primaryKey, $options = [])
 * @method AccessToken newEntity($data = null, array $options = [])
 * @method AccessToken[] newEntities(array $data, array $options = [])
 * @method AccessToken|bool save(EntityInterface $entity, $options = [])
 * @method AccessToken patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method AccessToken[] patchEntities($entities, array $data, array $options = [])
 */
class AccessTokensTable extends Table implements AccessTokenRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table('oauth_access_tokens');
        $this->setEntityClass('OAuthServer.AccessToken');
        $this->primaryKey('oauth_token'); // @TODO Update after running migrations?
        $this->hasMany('AccessTokenScopes', [
            'className'  => 'OAuthServer.AccessTokenScopes',
            'foreignKey' => 'oauth_token',
            'dependant'  => true,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $data = new AccessTokenData();
        $data->setClient($clientEntity);
        $data->setUserIdentifier($userIdentifier);
        foreach ($scopes as $scope) {
            $data->addScope($scope);
        }
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $entity = $this->newEntity([
            'oauth_token'         => $accessTokenEntity->getIdentifier(),
            'expires'             => $accessTokenEntity->getExpiryDateTime()->getTimestamp(),
            'client_id'           => $accessTokenEntity->getClient()->getIdentifier(),
            'user_id'             => $accessTokenEntity->getUserIdentifier(),
            'access_token_scopes' => map($accessTokenEntity->getScopes(), fn(ScopeEntityInterface $scope) => [
                'oauth_token' => $accessTokenEntity->getIdentifier(),
                'scope_id'    => $scope->getIdentifier(),
            ]),
        ]);
        $this->saveOrFail($entity);
    }

    /**
     * @inheritDoc
     */
    public function revokeAccessToken($tokenId)
    {
        if ($entity = $this->get($tokenId)) {
            $this->delete($entity);
        }
    }

    /**
     * @inheritDoc
     */
    public function isAccessTokenRevoked($tokenId)
    {
        return !$this
            ->find()
            ->where([$this->getPrimaryKey() => $tokenId])
            ->count();
    }
}
