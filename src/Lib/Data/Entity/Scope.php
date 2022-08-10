<?php

namespace OAuthServer\Lib\Data\Entity;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\ScopeTrait;

/**
 * OAuth 2.0 implementation Scope DTO
 */
class Scope implements ScopeEntityInterface
{
    use EntityTrait;
    use ScopeTrait;
}