<?php

namespace OAuthServer\Lib\Data\Entity;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

/**
 * OAuth 2.0 implementation Client DTO
 */
class Client implements ClientEntityInterface
{
    use EntityTrait;
    use ClientTrait;

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param string|string[] $redirectUri
     */
    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;
    }

    /**
     * @param bool $isConfidential
     */
    public function setIsConfidential(bool $isConfidential)
    {
        $this->isConfidential = $isConfidential;
    }
}