<?php

namespace OAuthServer\Model\Entity;

use Cake\ORM\Entity;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;

/**
 * OAuth 2.0 access token entity
 *
 * @property string                  $oauth_token
 * @property int                     $expires
 * @property string                  $client_id
 * @property string|null             $user_id
 *
 * @property AccessTokenScope[]|null $access_token_scopes
 */
class AccessToken extends Entity
{
    use AccessTokenTrait;
}