<?php

namespace OAuthServer\Model\Table;

use Cake\ORM\Table;

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
        $this->setTable('oauth_access_tokens');
        $this->setPrimaryKey('oauth_token');
        $this->belongsTo('Sessions', [
            'className' => 'OAuthServer.Sessions',
        ]);
        $this->hasMany('AccessTokenScopes', [
            'className' => 'OAuthServer.AccessTokenScopes',
            'foreignKey' => 'oauth_token',
            'dependant' => true,
        ]);
        parent::initialize($config);
    }
}
