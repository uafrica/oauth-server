<?php
namespace OAuthServer\Model\Table;

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
        $this->hasMany('SessionScopes', [
                'className' => 'OAuthServer.SessionScopes',
                'foreignKey' => 'session_id',
                'dependant' => true,
            ]);
        $this->hasMany('AuthCodes', [
            'className' => 'OAuthServer.AuthCodes',
            'foreignKey' => 'session_id',
            'dependant' => true,
        ]);
        $this->hasMany('AccessTokens', [
                'className' => 'OAuthServer.AccessTokens',
                'foreignKey' => 'session_id',
                'dependant' => true,
            ]);
        $this->hasMany('RefreshTokens', [
                'className' => 'OAuthServer.RefreshTokens',
                'foreignKey' => 'session_id',
                'dependant' => true,
            ]);
        $this->belongsTo('Clients', [
                'className' => 'OAuthServer.Clients',
                'foreignKey' => 'client_id',
            ]);
        parent::initialize($config);
    }
}
