<?php

namespace OAuthServer\Bridge\Entity;

use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * Implemented UserEntityInterface
 */
class User implements UserEntityInterface
{
    /**
     * @var string|int User identifier
     */
    private $id;

    public function __construct($identifier)
    {
        $this->id = $identifier;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier()
    {
        return $this->id;
    }
}
