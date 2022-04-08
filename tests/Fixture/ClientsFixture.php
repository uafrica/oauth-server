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
        'id' => ['type' => 'string', 'limit' => 20],
        'client_secret' => ['type' => 'string', 'limit' => 40, 'null' => false],
        'name' => ['type' => 'string', 'limit' => 200, 'null' => false],
        'redirect_uri' => ['type' => 'text', 'null' => false],
        'grant_types' => ['type' => 'text', 'null' => true],
        'created' => ['type' => 'timestamp', 'null' => true, 'default' => null],
        'modified' => ['type' => 'timestamp', 'null' => true, 'default' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
        ],
    ];

    public function init()
    {
        $this->records[] = [
            'id' => 'TEST',
            'client_secret' => 'TestSecret',
            'name' => 'Test',
            'redirect_uri' => json_encode(['http://www.example.com']),
            'grant_types' => json_encode([
                'client_credentials',
                'password',
                'authorization_code',
                'refresh_token',
            ]),
        ];

        $this->records[] = [
            'id' => 'AuthCodeOnly',
            'client_secret' => 'TestSecret',
            'name' => 'Test',
            'redirect_uri' => json_encode(['http://www.example.com']),
            'grant_types' => json_encode([
                'authorization_code',
                'refresh_token',
            ]),
        ];

        parent::init();
    }
}
