<?php

namespace OAuthServer\Bridge\Repository;

use Cake\Datasource\ModelAwareTrait;
use Cake\ORM\Table;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use OAuthServer\Model\Entity\AuthCode;
use OAuthServer\Model\Table\AuthCodesTable;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    use ModelAwareTrait;
    use RevokeTokenRepositoryTrait;

    /**
     * @var AuthCodesTable
     */
    private $table;

    /**
     * RefreshTokenRepository constructor.
     */
    public function __construct()
    {
        $this->table = $this->loadModel('OAuthServer.AuthCodes');
    }

    /**
     * {@inheritDoc}
     */
    public function getNewAuthCode()
    {
        return $this->table->newEntity([]);
    }

    /**
     * {@inheritDoc}
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        if ($this->table->exists([$this->table->getPrimaryKey() => $authCodeEntity->getIdentifier()])) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        if (!$authCodeEntity instanceof AuthCode) {
            $data = [
                'code' => $authCodeEntity->getIdentifier(),
                'user_id' => $authCodeEntity->getUserIdentifier(),
                'client_id' => $authCodeEntity->getClient()->getIdentifier(),
                'expires' => $authCodeEntity->getExpiryDateTime()->getTimestamp(),
                'scopes' => [],
            ];
            foreach ($authCodeEntity->getScopes() as $scope) {
                $data['scopes'][] = ['id' => $scope->getIdentifier()];
            }
            $authCodeEntity = $this->table->newEntity($data);
        }

        if (!$this->table->save($authCodeEntity)) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function revokeAuthCode($codeId)
    {
        if ($this->isAuthCodeRevoked($codeId)) {
            return;
        }

        $this->revokeToken($codeId);
    }

    /**
     * {@inheritDoc}
     */
    public function isAuthCodeRevoked($codeId): bool
    {
        return $this->isTokenRevoked($codeId);
    }

    /**
     * @return string
     */
    protected function getDeleteRecordOnRevokeKeyName(): string
    {
        return 'OAuthServer.deleteAuthCodeOnRevoke';
    }

    /**
     * @inheritDoc
     */
    protected function getTable(): Table
    {
        return $this->table;
    }
}
