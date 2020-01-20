<?php
/**
 * @copyright Martinus.sk
 * @author Jan Sukenik
 * @since 14. 2. 2017
 */

namespace OAuthServer\Test\Fixture\Model\Table\AccessTokensTable;

use Cake\I18n\FrozenTime;
use OAuthServer\Test\Fixture\RefreshTokensFixture as BaseFixture;

class RefreshTokensFixture extends BaseFixture
{
    public function init()
    {
        parent::init();
        $this->recprds = [];
        $this->records[] = [
            'refresh_token' => 'refresh_token1',
            'oauth_token' => 'expired_at_010000',
            'expires' => FrozenTime::now()->addHour()->getTimestamp(),
            'revoked' => false,
        ];
        $this->records[] = [
            'refresh_token' => 'refresh_token2',
            'oauth_token' => 'revoked1',
            'expires' => FrozenTime::now()->addHour()->getTimestamp(),
            'revoked' => false,
        ];
        $this->records[] = [
            'refresh_token' => 'refresh_token3',
            'oauth_token' => 'revoked2',
            'expires' => FrozenTime::now()->addHour()->getTimestamp(),
            'revoked' => false,
        ];
    }
}
