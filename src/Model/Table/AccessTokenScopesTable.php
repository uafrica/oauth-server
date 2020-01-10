<?php
namespace OAuthServer\Model\Table;

use Cake\ORM\Table;

class AccessTokenScopesTable extends Table
{

    /**
     * @param array $config Config
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('oauth_access_token_scopes');
        $this->belongsTo('AccessTokens', [
            'className' => 'OAuthServer.AccessTokens',
            'foreignKey' => 'oauth_token',
        ]);
        $this->belongsTo('Scopes', [
            'className' => 'OAuthServer.Scopes',
        ]);
        parent::initialize($config);
    }
}
