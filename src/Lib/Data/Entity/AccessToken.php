<?php

namespace OAuthServer\Lib\Data\Entity;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

/**
 * OAuth 2.0 implementation AccessToken DTO
 */
class AccessToken implements AccessTokenEntityInterface
{
    use AccessTokenTrait;
    use EntityTrait;
    use TokenEntityTrait;
}