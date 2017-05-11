<?php
namespace OAuthServer\Model\Table;

use Cake\Database\Schema\Table as SchemaTable;
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
        $this->table('oauth_clients');
        $this->primaryKey('id');
        $table = new SchemaTable(null);
        $table->addColumn('id', [
            'type' => 'string',
            'length' => 20,
            'null' => false
        ])->addColumn('client_secret', [
            'type' => 'string',
            'length' => 40,
            'null' => false
        ])->addColumn('name', [
            'type' => 'string',
            'length' => 200,
            'null' => false
        ])->addColumn('redirect_uri', [
            'type' => 'string',
            'length' => 255,
            'null' => false
        ])->addColumn('parent_model', [
            'type' => 'string',
            'length' => 200
        ])->addColumn('parent_id', [
            'type' => 'integer',
            'length' => 11,
            'null' => false
        ]);
        $this->schema($table);
        $this->displayField('name');
        $this->hasMany('Sessions', [
            'className' => 'OAuthServer.Sessions',
            'foreignKey' => 'client_id'
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
