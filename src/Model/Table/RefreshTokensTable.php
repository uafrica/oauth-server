<?php

namespace OAuthServer\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Table;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use OAuthServer\Model\Entity\RefreshToken;
use OAuthServer\Lib\Data\Entity\RefreshToken as RefreshTokenData;

/**
 * OAuth 2.0 refresh tokens table
 *
 * @property AccessTokensTable|BelongsTo $AccessTokens
 *
 * @method RefreshToken get($primaryKey, $options = [])
 * @method RefreshToken newEntity($data = null, array $options = [])
 * @method RefreshToken[] newEntities(array $data, array $options = [])
 * @method RefreshToken|bool save(EntityInterface $entity, $options = [])
 * @method RefreshToken patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method RefreshToken[] patchEntities($entities, array $data, array $options = [])
 */
class RefreshTokensTable extends Table implements RefreshTokenRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table('oauth_refresh_tokens');
        $this->setEntityClass('OAuthServer.RefreshToken');
        $this->primaryKey('refresh_token'); // @TODO Update after running migrations?
        $this->belongsTo('AccessTokens', [
            'className'  => 'OAuthServer.AccessTokens',
            'foreignKey' => 'oauth_token',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getNewRefreshToken()
    {
        return new RefreshTokenData();
    }

    /**
     * @inheritDoc
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        $entity = $this->newEntity([
            'refresh_token' => $refreshTokenEntity->getIdentifier(),
            'oauth_token'   => $refreshTokenEntity->getAccessToken()->getIdentifier(),
            'expires'       => $refreshTokenEntity->getExpiryDateTime()->getTimestamp(),
        ]);
        $this->saveOrFail($entity);
    }

    /**
     * @inheritDoc
     */
    public function revokeRefreshToken($tokenId)
    {
        if ($entity = $this->get($tokenId)) {
            $this->delete($entity);
        }
    }

    /**
     * @inheritDoc
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        return !$this
            ->find()
            ->where([
                $this->getPrimaryKey() => $tokenId,
            ])
            ->count();
    }
}
