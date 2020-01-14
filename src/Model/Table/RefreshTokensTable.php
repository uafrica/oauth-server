<?php

namespace OAuthServer\Model\Table;

use Cake\ORM\Table;

/**
 * RefreshToken Model
 *
 * @property Client $Client
 * @property User $User
 */
class RefreshTokensTable extends Table
{
    /**
     * @param array $config Config
     * @return void
     */
    public function initialize(array $config)
    {
        $this->setTable('oauth_refresh_tokens');
        $this->setPrimaryKey('refresh_token');
        $this->belongsTo('AccessTokens', [
            'className' => 'OAuthServer.AccessTokens',
            'foreignKey' => 'oauth_token',
        ]);
        parent::initialize($config);
    }
}
