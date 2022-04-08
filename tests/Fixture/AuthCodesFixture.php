<?php

namespace OAuthServer\Test\Fixture;

use Cake\I18n\FrozenTime;
use Cake\TestSuite\Fixture\TestFixture;

class AuthCodesFixture extends TestFixture
{
    public $table = 'oauth_auth_codes';

    public $fields = [
        'code' => ['type' => 'string', 'limit' => 80],
        'redirect_uri' => ['type' => 'string'],
        'expires' => ['type' => 'integer'],
        'client_id' => ['type' => 'string', 'null' => false, 'limit' => 20],
        'user_id' => ['type' => 'string', 'null' => false, 'limit' => 80],
        'revoked' => ['type' => 'boolean', 'default' => false, 'null' => false],
        'created' => ['type' => 'timestamp', 'null' => true, 'default' => null],
        'modified' => ['type' => 'timestamp', 'null' => true, 'default' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['code']],
        ],
    ];

    public function init()
    {
        $this->records[] = [
            'code' => 'exist_code_1',
            'expires' => FrozenTime::now()->addHour()->getTimestamp(),
            'client_id' => 'TEST',
            'user_id' => 'user1',
            'revoked' => false,
        ];
        parent::init();
    }
}
