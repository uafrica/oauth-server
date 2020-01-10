<?php
/**
 * @copyright Martinus.sk
 * @author Jan Sukenik
 * @since 14. 2. 2017
 */

namespace OAuthServer\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class SessionsFixture extends TestFixture
{
    public $table = 'oauth_sessions';
    public $fields = [
        'id' => ['type' => 'integer'],
        'owner_model' => ['type' => 'string'],
        'owner_id' => ['type' => 'string'],
        'client_id' => ['type' => 'string'],
        'client_redirect_uri' => ['type' => 'string'],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
        ],
    ];

    public $records = [];
}
