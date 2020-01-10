<?php

namespace OAuthServer\Model\Storage;

use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\ClientInterface;

class ClientStorage extends AbstractStorage implements ClientInterface
{
    /**
     * {@inheritdoc}
     *
     * @param string $clientId Client id
     * @param null $clientSecret Client secret
     * @param null $redirectUri Redirect uri
     * @param null $grantType Grant type
     * @return \League\OAuth2\Server\Entity\ClientEntity
     */
    public function get($clientId, $clientSecret = null, $redirectUri = null, $grantType = null)
    {
        $this->loadModel('OAuthServer.Clients');
        $query = $this->Clients->find()
            ->where([
                $this->Clients->aliasField('id') => $clientId,
            ]);

        if ($clientSecret !== null) {
            $query->where([$this->Clients->aliasField('client_secret') => $clientSecret]);
        }

        if ($redirectUri) {
            $query->where([$this->Clients->aliasField('redirect_uri') => $redirectUri]);
        }

        $result = $query->first();
        if ($result) {
            $client = new ClientEntity($this->server);
            $client->hydrate([
                'id' => $result->id,
                'name' => $result->name,
            ]);

            return $client;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param \League\OAuth2\Server\Entity\SessionEntity $session Session entity
     * @return \League\OAuth2\Server\Entity\ClientEntity
     */
    public function getBySession(SessionEntity $session)
    {
        $this->loadModel('OAuthServer.Sessions');
        $result = $this->Sessions->find()
            ->contain(['Clients'])
            ->where([
                'Sessions.id' => $session->getId(),
            ])
            ->first();

        if ($result) {
            $client = new ClientEntity($this->server);
            $client->hydrate([
                'id' => $result->client->id,
                'name' => $result->client->name,
            ]);

            return $client;
        }
    }
}
