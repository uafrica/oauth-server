<?php

namespace OAuthServer\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class AuthCodeScopesFixture extends TestFixture
{
    public $table = 'oauth_auth_code_scopes';

    public $fields = [
        'auth_code' => ['type' => 'string'],
        'scope_id' => ['type' => 'string'],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['auth_code', 'scope_id']],
        ],
    ];
}
