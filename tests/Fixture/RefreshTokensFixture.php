<?php
/**
 * @copyright Martinus.sk
 * @author Jan Sukenik
 * @since 14. 2. 2017
 */

namespace OAuthServer\Test\Fixture;

use Cake\I18n\FrozenTime;
use Cake\TestSuite\Fixture\TestFixture;

class RefreshTokensFixture extends TestFixture
{
    public $table = 'oauth_refresh_tokens';

    public $fields = [
        'refresh_token' => ['type' => 'string', 'null' => false, 'limit' => 80],
        'oauth_token' => ['type' => 'string', 'null' => false, 'limit' => 80],
        'expires' => ['type' => 'integer'],
        'revoked' => ['type' => 'boolean', 'default' => false, 'null' => false],
        'created' => ['type' => 'timestamp', 'null' => true, 'default' => null],
        'modified' => ['type' => 'timestamp', 'null' => true, 'default' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['refresh_token']],
        ],
    ];

    public function init()
    {
        $this->records[] = [
            'refresh_token' => 'exist_refresh_token_1',
            'oauth_token' => 'exist_token_1',
            'expires' => FrozenTime::now()->addHour()->getTimestamp(),
            'revoked' => false,
        ];
        parent::init();
    }
}
