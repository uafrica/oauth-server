<?php
namespace OAuthServer\Model\Table;

use Cake\ORM\Table;
use Cake\Database\Schema\Table as SchemaTable;

class AccessTokenScopesTable extends Table
{

    /**
     * @param array $config Config
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('oauth_access_token_scopes');
        $table = new SchemaTable(null);
        $table
        	->addColumn('id', [
        		'type' => 'integer',
        		'length' => 11,
        		'null' => false
        	])->addColumn('oauth_token', [
        		'type' => 'string',
        		'length' => 40,
        		'null' => false
        	])->addColumn('scope_id', [
        		'type' => 'string',
        		'length' => 40,
        		'null' => false
        	]);
        $this->schema($table);
        $this->belongsTo('AccessTokens', [
            'className' => 'OAuthServer.AccessTokens',
            'foreignKey' => 'oauth_token'
        ]);
        $this->belongsTo('Scopes', [
            'className' => 'OAuthServer.Scopes'
        ]);
        parent::initialize($config);
    }
}