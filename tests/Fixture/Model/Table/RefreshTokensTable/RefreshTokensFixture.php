<?php
/**
 * @copyright Martinus.sk
 * @author Jan Sukenik
 * @since 14. 2. 2017
 */

namespace OAuthServer\Test\Fixture\Model\Table\RefreshTokensTable;

use Cake\I18n\FrozenTime;
use OAuthServer\Test\Fixture\RefreshTokensFixture as BaseFixture;

class RefreshTokensFixture extends BaseFixture
{
    public function init()
    {
        parent::init();
        $this->recprds = [];
        $this->records[] = [
            'refresh_token' => 'expired_at_005959',
            'oauth_token' => 'oauth_token1',
            'expires' => (new FrozenTime('2020-01-01 00:59:59'))->getTimestamp(),
            'revoked' => false,
        ];
        $this->records[] = [
            'refresh_token' => 'expired_at_010000',
            'oauth_token' => 'oauth_token2',
            'expires' => (new FrozenTime('2020-01-01 01:00:00'))->getTimestamp(),
            'revoked' => false,
        ];
        $this->records[] = [
            'refresh_token' => 'expired_at_010001',
            'oauth_token' => 'oauth_token3',
            'expires' => (new FrozenTime('2020-01-01 01:00:01'))->getTimestamp(),
            'revoked' => false,
        ];
        $this->records[] = [
            'refresh_token' => 'revoked1',
            'oauth_token' => 'oauth_token4',
            'expires' => (new FrozenTime('2020-01-01 01:00:02'))->getTimestamp(),
            'revoked' => true,
        ];
        $this->records[] = [
            'refresh_token' => 'revoked2',
            'oauth_token' => 'oauth_token5',
            'expires' => (new FrozenTime('2020-01-01 01:00:03'))->getTimestamp(),
            'revoked' => true,
        ];
    }
}
