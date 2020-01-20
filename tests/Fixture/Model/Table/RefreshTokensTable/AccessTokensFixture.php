<?php
/**
 * @copyright Martinus.sk
 * @author Jan Sukenik
 * @since 14. 2. 2017
 */

namespace OAuthServer\Test\Fixture\Model\Table\RefreshTokensTable;

use Cake\I18n\FrozenTime;
use OAuthServer\Test\Fixture\AccessTokensFixture as BaseFixture;

class AccessTokensFixture extends BaseFixture
{
    public function init()
    {
        parent::init();
        $this->records = [];
        $this->records[] = [
            'oauth_token' => 'oauth_token1',
            'expires' => (new FrozenTime('2020-01-01 01:59:59'))->getTimestamp(),
            'client_id' => 'TEST',
            'user_id' => 'user1',
            'revoked' => false,
        ];
        $this->records[] = [
            'oauth_token' => 'oauth_token2',
            'expires' => (new FrozenTime('2020-01-01 01:00:00'))->getTimestamp(),
            'client_id' => 'TEST',
            'user_id' => 'user1',
            'revoked' => false,
        ];
        $this->records[] = [
            'oauth_token' => 'oauth_token3',
            'expires' => (new FrozenTime('2020-01-01 01:00:01'))->getTimestamp(),
            'client_id' => 'TEST',
            'user_id' => 'user1',
            'revoked' => false,
        ];
        $this->records[] = [
            'oauth_token' => 'oauth_token4',
            'expires' => (new FrozenTime('2020-01-01 01:00:02'))->getTimestamp(),
            'client_id' => 'TEST',
            'user_id' => 'user1',
            'revoked' => false,
        ];
        $this->records[] = [
            'oauth_token' => 'oauth_token5',
            'expires' => (new FrozenTime('2020-01-01 01:00:03'))->getTimestamp(),
            'client_id' => 'TEST',
            'user_id' => 'user2',
            'revoked' => false,
        ];
    }
}
