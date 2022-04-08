<?php

namespace OAuthServer\Bridge\Repository;

use Cake\Datasource\ModelAwareTrait;
use Cake\ORM\Table;
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
    use RevokeTokenRepositoryTrait;

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

        $this->revokeToken($tokenId);
    }

    /**
     * {@inheritDoc}
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        return $this->isTokenRevoked($tokenId);
    }

    /**
     * @return string
     */
    protected function getDeleteRecordOnRevokeKeyName(): string
    {
        return 'OAuthServer.deleteRefreshTokenOnRevoke';
    }

    /**
     * @inheritDoc
     */
    protected function getTable(): Table
    {
        return $this->table;
    }
}
