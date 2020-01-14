<?php

namespace OAuthServer\Model\Bridge\Repository;

use Cake\Datasource\ModelAwareTrait;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use OAuthServer\Model\Entity\RefreshToken;
use OAuthServer\Model\Table\RefreshTokensTable;

/**
 * implemented RefreshTokenRepositoryInterface
 */
class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    use ModelAwareTrait;

    /**
     * @var RefreshTokensTable
     */
    private $table;

    /**
     * RefreshTokenRepository constructor.
     */
    public function __construct()
    {
        $this->table = $this->loadModel('OAuthServer.RefreshTokens');
    }

    /**
     * {@inheritDoc}
     */
    public function getNewRefreshToken()
    {
        return $this->table->newEntity([]);
    }

    /**
     * {@inheritDoc}
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        if ($this->table->exists([$this->table->getPrimaryKey() => $refreshTokenEntity->getIdentifier()])) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        if (!$refreshTokenEntity instanceof RefreshToken) {
            $data = [
                'refresh_token' => $refreshTokenEntity->getIdentifier(),
                'oauth_token' => $refreshTokenEntity->getAccessToken()->getIdentifier(),
                'expires' => $refreshTokenEntity->getExpiryDateTime()->getTimestamp(),
            ];
            $refreshTokenEntity = $this->table->newEntity($data);
        }

        if (!$this->table->save($refreshTokenEntity)) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function revokeRefreshToken($tokenId)
    {
        if ($this->isRefreshTokenRevoked($tokenId)) {
            return;
        }

        $token = $this->table->get($tokenId);
        $token->revoked = true;

        $this->table->save($token);
    }

    /**
     * {@inheritDoc}
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        $conditions = [
            $this->table->aliasField($this->table->getPrimaryKey()) => $tokenId,
            $this->table->aliasField('revoked') => false,
        ];

        return !$this->table->exists($conditions);
    }
}
