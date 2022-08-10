<?php

namespace OAuthServer\Model\Entity;

use Cake\ORM\Entity;

/**
 * OAuth 2.0 refresh token entity
 *
 * @property string           $refresh_token
 * @property string           $oauth_token
 * @property int              $expires
 *
 * @property AccessToken|null $access_token
 */
class RefreshToken extends Entity
{

}