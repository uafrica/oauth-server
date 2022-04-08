<?php

namespace OAuthServer\Model\Entity;

use Cake\ORM\Entity;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

/**
 * implemented ScopeEntityInterface
 *
 * @property string $id
 * @property string $description
 */
class Scope extends Entity implements ScopeEntityInterface
{
    /**
     * @inheritDoc
     */
    public function getIdentifier()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getIdentifier();
    }

    /**
     * @return array|mixed|string
     */
    public function jsonSerialize()
    {
        return (string)$this;
    }
}
