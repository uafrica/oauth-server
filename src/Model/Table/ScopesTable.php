<?php
namespace OAuthServer\Model\Table;

use Cake\Database\Schema\Table as SchemaTable;
use Cake\ORM\Table;

class ScopesTable extends Table
{
    /**
     * @param array $config Config
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('oauth_scopes');
        $this->primaryKey('id');
        $table = new SchemaTable(null);
        $table->addColumn('id', [
            'type' => 'string',
            'length' => 40,
            'null' => false
        ])->addColumn('description', [
            'type' => 'string',
            'length' => 200,
            'null' => false
        ]);
        $this->schema($table);
        $this->hasMany('AccessTokenScopes', [
            'className' => 'OAuthServer.AccessTokenScopes'
        ]);
        $this->hasMany('AuthCodeScopes', [
                'className' => 'OAuthServer.AuthCodeScopes'
            ]);
        $this->hasMany('SessionScopes', [
                'className' => 'OAuthServer.SessionScopes'
            ]);
        parent::initialize($config);
    }
}
