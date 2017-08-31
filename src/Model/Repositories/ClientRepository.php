<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace OAuthServer\Model\Repositories;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use OAuthServer\Model\Entities\ClientEntity;

class ClientRepository implements ClientRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null, $mustValidateSecret = true)
    {
        $clients = TableRegistry::get('OAuthServer.Clients');
        $client = $clients->find()
        ->where(['id' => $clientIdentifier])->first();
        // $clients = $this->
        // $clients = [
        //     'myawesomeapp' => [
        //         'secret'          => password_hash('abc123', PASSWORD_BCRYPT),
        //         'name'            => 'My Awesome App',
        //         'redirect_uri'    => 'http://foo/bar',
        //         'is_confidential' => true,
        //     ],
        // ];

        // Check if client is registered
        if (!isset($client)) {
            return;
        }

        // if (
        //     $mustValidateSecret === true
        //     && $clients[$clientIdentifier]['is_confidential'] === true
        //     && password_verify($clientSecret, $clients[$clientIdentifier]['secret']) === false
        // ) {
        //     return;
        // }
        $clientName = $client['name'];
        $clientRedirectUri = $client['redirect_uri'];
        $client = new ClientEntity();
        $client->setIdentifier($clientIdentifier);
        $client->setName($clientName);
        $client->setRedirectUri($clientRedirectUri);

        return $client;
    }
}
