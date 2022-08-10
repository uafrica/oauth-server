<?php

namespace OAuthServer\Lib\Data\Entity;

use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * OAuth 2.0 implementation User DTO
 */
class User implements UserEntityInterface
{
    use EntityTrait;
}