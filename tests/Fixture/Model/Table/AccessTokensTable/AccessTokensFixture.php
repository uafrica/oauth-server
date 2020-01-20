<?php
/**
 * @copyright Martinus.sk
 * @author Jan Sukenik
 * @since 14. 2. 2017
 */

namespace OAuthServer\Test\Fixture\Model\Table\AccessTokensTable;

use Cake\I18n\FrozenTime;
use OAuthServer\Test\Fixture\AccessTokensFixture as BaseFixture;

class AccessTokensFixture extends BaseFixture
{
    public function init()
    {
        parent::init();
        $this->records = [];
        $this->records[] = [
            'oauth_token' => 'expired_at_005959',
            'expires' => (new FrozenTime('2020-01-01 00:59:59'))->getTimestamp(),
            'client_id' => 'TEST',
            'user_id' => 'user1',
            'revoked' => false,
        ];
        $this->records[] = [
            'oauth_token' => 'expired_at_010000',
            'expires' => (new FrozenTime('2020-01-01 01:00:00'))->getTimestamp(),
            'client_id' => 'TEST',
            'user_id' => 'user1',
            'revoked' => false,
        ];
        $this->records[] = [
            'oauth_token' => 'expired_at_010001',
            'expires' => (new FrozenTime('2020-01-01 01:00:01'))->getTimestamp(),
            'client_id' => 'TEST',
            'user_id' => 'user1',
            'revoked' => false,
        ];
        $this->records[] = [
            'oauth_token' => 'revoked1',
            'expires' => (new FrozenTime('2020-01-01 01:00:02'))->getTimestamp(),
            'client_id' => 'TEST',
            'user_id' => 'user1',
            'revoked' => true,
        ];
        $this->records[] = [
            'oauth_token' => 'revoked2',
            'expires' => (new FrozenTime('2020-01-01 01:00:03'))->getTimestamp(),
            'client_id' => 'TEST',
            'user_id' => 'user2',
            'revoked' => true,
        ];
    }
}
