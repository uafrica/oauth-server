<?php
/**
 * @copyright Martinus.sk
 * @author Jan Sukenik
 * @since 13. 2. 2017
 */

namespace OAuthServer\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class ClientsFixture extends TestFixture
{
    public $table = 'oauth_clients';
    public $fields = [
        'id' => ['type' => 'string'],
        'client_secret' => ['type' => 'string'],
        'name' => ['type' => 'string'],
        'redirect_uri' => ['type' => 'string'],
        'parent_model' => ['type' => 'string'],
        'parent_id' => ['type' => 'integer'],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
        ],
    ];

    public $records = [
        [
            'id' => 'TEST',
            'client_secret' => 'TestSecret',
            'name' => 'Test',
            'redirect_uri' => 'http://www.example.com',
        ],
    ];
}
