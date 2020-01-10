<?php
namespace OAuthServer\Model\Table;

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
        $this->hasMany('AccessTokenScopes', [
            'className' => 'OAuthServer.AccessTokenScopes',
        ]);
        $this->hasMany('AuthCodeScopes', [
                'className' => 'OAuthServer.AuthCodeScopes',
            ]);
        $this->hasMany('SessionScopes', [
                'className' => 'OAuthServer.SessionScopes',
            ]);
        parent::initialize($config);
    }
}
