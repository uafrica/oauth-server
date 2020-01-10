<?php

namespace OAuthServer\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class SessionScopesFixture extends TestFixture
{
    public $table = 'oauth_session_scopes';
    public $fields = [
        'id' => ['type' => 'integer'],
        'session_id' => ['type' => 'integer'],
        'scope_id' => ['type' => 'string'],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
        ],
    ];

    public $records = [

    ];
}
