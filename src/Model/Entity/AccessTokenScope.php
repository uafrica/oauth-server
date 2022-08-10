<?php

namespace OAuthServer\Model\Entity;

use Cake\ORM\Entity;

/**
 * OAuth 2.0 access tokens to scopes join table entity
 *
 * @property string           $oauth_token
 * @property string           $scope_id
 *
 * @property AccessToken|null $access_token
 * @property Scope|null       $scope
 */
class AccessTokenScope extends Entity
{

}

