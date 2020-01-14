<?php

namespace OAuthServer\Model\Table;

use Cake\ORM\Table;

/**
 * AuthCode Model
 *
 * @property Client $Client
 * @property User $User
 */
class AuthCodesTable extends Table
{
    /**
     * @param array $config Config
     * @return void
     */
    public function initialize(array $config)
    {
        $this->setTable('oauth_auth_codes');
        $this->setPrimaryKey('code');

        $this->belongsTo(
            'Sessions',
            [
                'className' => 'OAuthServer.Sessions',
                'foreignKey' => 'session_id',
            ]
        );
        $this->hasMany(
            'AuthCodeScopes',
            [
                'className' => 'OAuthServer.AuthCodeScopes',
                'foreignKey' => 'auth_code',
                'dependant' => true,
            ]
        );
        parent::initialize($config);
    }
}
