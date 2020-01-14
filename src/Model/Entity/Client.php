<?php

namespace OAuthServer\Model\Entity;

use Cake\ORM\Entity;
use League\OAuth2\Server\Entities\ClientEntityInterface;

/**
 * implemented ClientEntityInterface
 *
 * @property string $id the client's identifier
 * @property string|null $client_secret the client's secret key
 * @property string $name the client's name
 * @property string|string[] $redirect_uri Returns the registered redirect URI
 * @property string[] $grant_types
 */
class Client extends Entity implements ClientEntityInterface
{
    protected $_accessible = [
        'name' => true,
        'redirect_uri' => true,
        'grant_types' => true,
    ];

    protected $_hidden = [
        'client_secret',
    ];

    /**
     * @inheritDoc
     */
    public function getIdentifier()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

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
    public function isConfidential()
    {
        return !empty($this->client_secret);
    }
}
