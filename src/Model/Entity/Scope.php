<?php

namespace OAuthServer\Model\Entity;

use Cake\ORM\Entity;
use OAuthServer\Lib\Data\Entity\Scope as ScopeData;

/**
 * OAuth 2.0 scope entity
 *
 * @property string $id
 * @property string $description
 */
class Scope extends Entity
{
    /**
     * Transforms the ORM Entity object into an OAuth 2.0 server DTO object
     *
     * @return ScopeData
     */
    public function transformToDTO(): ScopeData
    {
        $dto = new ScopeData();
        $dto->setIdentifier($this->id);
        return $dto;
    }
}