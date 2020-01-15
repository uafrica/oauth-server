<?php

namespace OAuthServer\Model\Entity;

use Cake\ORM\Entity;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use OAuthServer\Bridge\Entity\ExpiryDateTimeTrait;
use OAuthServer\Bridge\Entity\TokenEntityTrait;
use OAuthServer\Bridge\Entity\User;

/**
 * implemented AccessTokenEntityInterface
 *
 * @property string $oauth_token
 * @property int $expires
 * @property string $client_id
 * @property string|int $user_id
 * @property bool $revoked
 * @property Client $client
 * @property User $user
 * @property Scope[] $scopes
 */
class AccessToken extends Entity implements AccessTokenEntityInterface
{
    use AccessTokenTrait;
    use ExpiryDateTimeTrait;
    use TokenEntityTrait;

    protected $_accessible = [
        'oauth_token' => true,
        'expires' => true,
        'client_id' => true,
        'user_id' => true,
        'scopes' => true,
        'revoked' => false,
    ];

    /**
     * @inheritDoc
     */
    public function getIdentifier()
    {
        return $this->oauth_token;
    }

    /**
     * @inheritDoc
     */
    public function setIdentifier($identifier)
    {
        $this->oauth_token = $identifier;
    }
}
