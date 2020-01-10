<?php
/**
 * @copyright Martinus.sk
 * @author Jan Sukenik
 * @since 14. 2. 2017
 */

namespace OAuthServer\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class AccessTokensFixture extends TestFixture
{
    public $table = 'oauth_access_tokens';
    public $fields = [
        'oauth_token' => ['type' => 'string'],
        'session_id' => ['type' => 'integer'],
        'expires' => ['type' => 'integer'],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['oauth_token']],
        ],
    ];

    public $records = [];
}
