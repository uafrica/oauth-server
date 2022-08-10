<?php

namespace OAuthServer\Model\Entity;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use OAuthServer\Lib\Data\Entity\Client as ClientData;

/**
 * OAuth 2.0 client entity
 *
 * @property string          $id
 * @property string          $client_secret
 * @property string          $name
 * @property string          $redirect_uri
 * @property string|null     $parent_model
 * @property int|null        $parent_id
 * @property EntityInterface $parent
 *
 * @property Client[]|null   $clients
 */
class Client extends Entity
{
    /**
     * When there is an implicit belongsTo relationship this virtual
     * property getter will return the associated entity
     *
     * @return EntityInterface|null
     */
    protected function _getParent(): ?EntityInterface
    {
        if (empty($this->parent_model)) {
            return null;
        }
        $table = TableRegistry::get($this->parent_model);
        return $table->get($this->parent_id);
    }

    /**
     * Transforms the ORM Entity object into an OAuth 2.0 server DTO object
     *
     * @return ClientData
     */
    public function transformToDTO(): ClientData
    {
        $dto = new ClientData();
        $dto->setIdentifier($this->id);
        $dto->setName($this->name);
        $dto->setRedirectUri($this->redirect_uri);
        $dto->setIsConfidential(!empty($this->client_secret));
        return $dto;
    }
}