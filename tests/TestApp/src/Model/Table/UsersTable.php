<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use OAuthServer\Lib\Data\Entity\User;

/**
 * CLASS FOR TESTING PURPOSES
 */
class UsersTable extends Table implements UserRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity)
    {
        $entity = $this
            ->find()
            ->where(compact('username', 'password'))
            ->first();
        $data   = new User();
        $data->setIdentifier($entity->id);
        return $data;
    }
}