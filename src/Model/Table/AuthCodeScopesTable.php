<?php
namespace OAuthServer\Model\Table;

use Cake\ORM\Table;
use Cake\Database\Schema\Table as SchemaTable;

class AuthCodeScopesTable extends Table
{
    /**
     * @param array $config Config
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('oauth_auth_code_scopes');
        $table = new SchemaTable(null);
        $table
        	->addColumn('id', [
        		'type' => 'integer',
        		'length' => 11,
        		'null' => false
        	])->addColumn('auth_code', [
        		'type' => 'string',
        		'length' => 40,
        		'null' => false
        	])->addColumn('scope_id', [
        		'type' => 'string',
        		'length' => 40,
        		'null' => false
        	]);
        $this->schema($table);
        $this->belongsTo('AuthCodes', [
                'className' => 'OAuthServer.AuthCodes',
                'foreignKey' => 'auth_code',
                'propertyName' => 'code'
            ]);
        $this->belongsTo('Scopes', [
                'className' => 'OAuthServer.Scopes'
            ]);
        parent::initialize($config);
    }
}