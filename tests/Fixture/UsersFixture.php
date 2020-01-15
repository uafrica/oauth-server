<?php
/**
 * @copyright Martinus.sk
 * @author Jan Sukenik
 * @since 14. 2. 2017
 */

namespace OAuthServer\Test\Fixture;

use Cake\Auth\DefaultPasswordHasher;
use Cake\I18n\FrozenTime;
use Cake\TestSuite\Fixture\TestFixture;

class UsersFixture extends TestFixture
{
    public $table = 'users';

    public $fields = [
        'id' => ['type' => 'string', 'null' => false, 'limit' => 36],
        'name' => ['type' => 'string'],
        'email' => ['type' => 'string'],
        'password' => ['type' => 'string'],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
        ],
    ];

    public function init()
    {
        $hasher = new DefaultPasswordHasher();

        $this->records[] = [
            'id' => 'user1',
            'name' => 'Alice',
            'email' => 'user1@example.com',
            'password' => $hasher->hash('123456'),
        ];
        $this->records[] = [
            'id' => 'user2',
            'name' => 'Bob',
            'email' => 'user2@example.com',
            'password' => $hasher->hash('654321'),
        ];

        parent::init();
    }
}
