<?php

namespace OAuthServer\Model\Entity;

use Cake\ORM\Entity;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use OAuthServer\Bridge\Entity\ExpiryDateTimeTrait;
use OAuthServer\Bridge\Entity\TokenEntityTrait;
use OAuthServer\Bridge\Entity\User;

/**
 * Class AuthCode
 *
 * @property string $code
 * @property string|null $redirect_uri
 * @property int $expires
 * @property string $client_id
 * @property string|int $user_id
 * @property bool $revoked
 * @property Client $client
 * @property User $user
 * @property Scope[] $scopes
 */
class AuthCode extends Entity implements AuthCodeEntityInterface
{
    use ExpiryDateTimeTrait;
    use TokenEntityTrait;

    protected $_accessible = [
        'code' => true,
        'redirect_uri' => true,
        'expires' => true,
        'client_id' => true,
        'user_id' => true,
        'scopes' => true,
        'revoked' => false,
    ];

    /**
     * @inheritDoc
     */
    public function getRedirectUri()
    {
        return $this->redirect_uri;
    }

    /**
     * @inheritDoc
     */
    public function setRedirectUri($uri)
    {
        $this->redirect_uri = $uri;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier()
    {
        return $this->code;
    }

    /**
     * @inheritDoc
     */
    public function setIdentifier($identifier)
    {
        $this->code = $identifier;
    }
}
