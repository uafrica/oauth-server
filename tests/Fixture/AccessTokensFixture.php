<?php
/**
 * @copyright Martinus.sk
 * @author Jan Sukenik
 * @since 14. 2. 2017
 */

namespace OAuthServer\Test\Fixture;

use Cake\I18n\FrozenTime;
use Cake\TestSuite\Fixture\TestFixture;

class AccessTokensFixture extends TestFixture
{
    public $table = 'oauth_access_tokens';

    public $fields = [
        'oauth_token' => ['type' => 'string', 'null' => false, 'limit' => 40],
        'expires' => ['type' => 'integer'],
        'client_id' => ['type' => 'string', 'null' => false, 'limit' => 20],
        'user_id' => ['type' => 'string', 'null' => false, 'limit' => 36],
        'revoked' => ['type' => 'boolean', 'default' => false, 'null' => false],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['oauth_token']],
        ],
    ];

    public function init()
    {
        $this->records[] = [
            'oauth_token' => 'exist_token_1',
            'expires' => FrozenTime::now()->addHour()->getTimestamp(),
            'client_id' => 'TEST',
            'user_id' => 'user1',
            'revoked' => false,
        ];
        parent::init();
    }
}
