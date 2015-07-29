<?php

namespace OAuthServer\Model\Table;

use Cake\ORM\Table;
use Cake\Database\Schema\Table as SchemaTable;

/**
 * AccessToken Model
 *
 * @property Client $Client
 * @property User $User
 */
class AccessTokensTable extends Table
{
    /**
     * @param array $config Config
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('oauth_access_tokens');
        $this->primaryKey('oauth_token');
        $table = new SchemaTable(null);
        $table
        	->addColumn('oauth_token', [
        		'type' => 'string',
        		'length' => 40,
        		'null' => false
        	])->addColumn('session_id', [
        		'type' => 'integer',
        		'length' => 11,
        		'null' => false
        	])->addColumn('expires', [
        		'type' => 'integer',
        		'length' => 11,
        		'null' => false
        	]);
        $this->schema($table);
        $this->belongsTo('Sessions', [
            'className' => 'OAuthServer.Sessions',
        ]);
        $this->hasMany('AccessTokenScopes', [
            'className' => 'OAuthServer.AccessTokenScopes',
            'foreignKey' => 'oauth_token',
            'dependant' => true
        ]);
        parent::initialize($config);
    }
}
