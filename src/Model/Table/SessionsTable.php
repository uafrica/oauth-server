<?php
namespace OAuthServer\Model\Table;

use Cake\Database\Schema\Table as SchemaTable;
use Cake\ORM\Table;

class SessionsTable extends Table
{
    /**
     * @param array $config Config
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('oauth_sessions');
        $this->primaryKey('id');
        $table = new SchemaTable(null);
        $table->addColumn('id', [
            'type' => 'integer',
            'length' => 11,
            'null' => false
        ])->addColumn('owner_model', [
            'type' => 'string',
            'length' => 200,
            'null' => false
        ])->addColumn('owner_id', [
            'type' => 'integer',
            'length' => 11,
            'null' => false
        ])->addColumn('client_id', [
            'type' => 'string',
            'length' => 20,
            'null' => false
        ])->addColumn('client_redirect_uri', [
            'type' => 'string',
            'length' => 200
        ]);
        $this->schema($table);
        $this->hasMany('SessionScopes', [
                'className' => 'OAuthServer.SessionScopes',
                'foreignKey' => 'session_id',
                'dependant' => true
            ]);
        $this->hasMany('AuthCodes', [
            'className' => 'OAuthServer.AuthCodes',
            'foreignKey' => 'session_id',
            'dependant' => true
        ]);
        $this->hasMany('AccessTokens', [
                'className' => 'OAuthServer.AccessTokens',
                'foreignKey' => 'session_id',
                'dependant' => true
            ]);
        $this->hasMany('RefreshTokens', [
                'className' => 'OAuthServer.RefreshTokens',
                'foreignKey' => 'session_id',
                'dependant' => true
            ]);
        $this->belongsTo('Clients', [
                'className' => 'OAuthServer.Clients',
                'foreignKey' => 'client_id'
            ]);
        parent::initialize($config);
    }
}
