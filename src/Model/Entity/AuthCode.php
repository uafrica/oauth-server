<?php

namespace OAuthServer\Model\Entity;

use Cake\ORM\Entity;

/**
 * OAuth 2.0 authorisation code entity
 *
 * @property string               $code
 * @property string               $redirect_uri
 * @property int                  $expires
 * @property string               $client_id
 * @property string|null          $user_id
 *
 * @property AuthCodeScope[]|null $auth_code_scopes
 */
class AuthCode extends Entity
{

}