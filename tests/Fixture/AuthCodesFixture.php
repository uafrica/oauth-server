<?php

namespace OAuthServer\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class AuthCodesFixture extends TestFixture
{
    public $table = 'oauth_auth_codes';
    public $fields = [
        'code' => ['type' => 'string'],
        'session_id' => ['type' => 'integer'],
        'redirect_uri' => ['type' => 'string'],
        'expires' => ['type' => 'integer'],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['code']],
        ],
    ];
}
