<?php

namespace OAuthServer\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class AccessTokenScopesFixture extends TestFixture
{
    public $table = 'oauth_access_token_scopes';

    public $fields = [
        'oauth_token' => ['type' => 'string'],
        'scope_id' => ['type' => 'string'],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['oauth_token', 'scope_id']],
        ],
    ];
}
