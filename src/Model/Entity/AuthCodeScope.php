<?php

namespace OAuthServer\Model\Entity;

use Cake\ORM\Entity;

/**
 * OAuth 2.0 authorisation codes to scopes join table entity
 *
 * @property string        $auth_code
 * @property string        $scope_id
 *
 * @property AuthCode|null $code
 * @property Scope|null    $scope
 */
class AuthCodeScope extends Entity
{

}