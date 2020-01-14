<?php
namespace OAuthServer\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Table;
use OAuthServer\Model\Entity\Client;

/**
 * Client Model
 *
 * @property AccessToken $AccessToken
 * @property AuthCode $AuthCode
 * @property RefreshToken $RefreshToken
 */
class ClientsTable extends Table
{
    /**
     * @param array $config Config
     * @return void
     */
    public function initialize(array $config)
    {
        $this->setTable('oauth_clients');
        $this->setPrimaryKey('id');
        $this->setDisplayField('name');
        $this->hasMany('Sessions', [
            'className' => 'OAuthServer.Sessions',
            'foreignKey' => 'client_id',
        ]);
        parent::initialize($config);
    }

    /**
     * @param \Cake\Event\Event $event Event object
     * @param \OAuthServer\Model\Entity\Client $client Client entity
     * @return void
     */
    public function beforeSave(Event $event, Client $client)
    {
        if ($client->isNew()) {
            $client->id = base64_encode(uniqid() . substr(uniqid(), 11, 2));// e.g. NGYcZDRjODcxYzFkY2Rk (seems popular format)
            $client->generateSecret();
        }
    }
}
