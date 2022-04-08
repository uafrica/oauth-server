<?php

namespace OAuthServer\Bridge\Repository;

use Cake\Core\Configure;
use Cake\ORM\Table;

/**
 * For Token repository
 */
trait RevokeTokenRepositoryTrait
{
    /**
     * return Token's table
     *
     * @return Table
     */
    abstract protected function getTable(): Table;

    /**
     * return Configuration key name that controlling deleting record on revoke token.
     *
     * @return string
     */
    abstract protected function getDeleteRecordOnRevokeKeyName(): string;

    /**
     * @param string $tokenId target token id
     * @return void
     */
    protected function revokeToken($tokenId): void
    {
        $token = $this->getTable()->get($tokenId);

        if ($this->getDeleteRecordOnRevoke()) {
            $this->getTable()->delete($token);
        }

        $token->revoked = true;

        $this->getTable()->save($token);
    }

    /**
     * @param string $tokenId target token id
     * @return bool
     */
    protected function isTokenRevoked($tokenId): bool
    {
        $conditions = [
            $this->getTable()->aliasField($this->getTable()->getPrimaryKey()) => $tokenId,
        ];
        $entity = $this->getTable()->find()->where($conditions)->first();

        return ($entity === null) || $entity->revoked;
    }

    /**
     * @param bool $delete If set to true, Delete record on revoke token.
     * @return void
     */
    public function setDeleteRecordOnRevoke($delete): void
    {
        Configure::write($this->getDeleteRecordOnRevokeKeyName(), $delete);
    }

    /**
     * @return bool
     */
    public function getDeleteRecordOnRevoke(): bool
    {
        return Configure::read($this->getDeleteRecordOnRevokeKeyName(), false);
    }
}
