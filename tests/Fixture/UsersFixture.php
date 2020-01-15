<?php
/**
 * @copyright Martinus.sk
 * @author Jan Sukenik
 * @since 14. 2. 2017
 */

namespace OAuthServer\Test\Fixture;

use Cake\I18n\FrozenTime;
use Cake\TestSuite\Fixture\TestFixture;

class UsersFixture extends TestFixture
{
    public $table = 'users';

    public $fields = [
        'id' => ['type' => 'string', 'null' => false, 'limit' => 36],
        'name' => ['type' => 'string'],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
        ],
    ];

    public function init()
    {
        $this->records[] = [
            'id' => 'user1',
            'name' => 'Alice',
        ];
        $this->records[] = [
            'id' => 'user2',
            'name' => 'Bob',
        ];

        parent::init();
    }
}
