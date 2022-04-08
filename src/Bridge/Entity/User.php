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

    /**
     * User constructor.
     *
     * @param string|int $identifier the user identifier
     */
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
