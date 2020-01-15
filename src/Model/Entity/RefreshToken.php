<?php

namespace OAuthServer\Model\Entity;

use Cake\ORM\Entity;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use OAuthServer\Bridge\Entity\ExpiryDateTimeTrait;

/**
 * implemented RefreshTokenEntityInterface
 *
 * @property string $refresh_token
 * @property string $oauth_token
 * @property int $expires
 * @property bool $revoked
 * @property AccessToken $access_token
 */
class RefreshToken extends Entity implements RefreshTokenEntityInterface
{
    use ExpiryDateTimeTrait;

    protected $_accessible = [
        'refresh_token' => true,
        'oauth_token' => true,
        'expires' => true,
        'revoked' => false,
    ];

    /**
     * @inheritDoc
     */
    public function getIdentifier()
    {
        return $this->refresh_token;
    }

    /**
     * @inheritDoc
     */
    public function setIdentifier($identifier)
    {
        $this->refresh_token = $identifier;
    }

    /**
     * @inheritDoc
     */
    public function setAccessToken(AccessTokenEntityInterface $accessToken)
    {
        $this->access_token = $accessToken;
        $this->oauth_token = $accessToken->getIdentifier();
    }

    /**
     * @inheritDoc
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }
}
