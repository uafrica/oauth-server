<?php

namespace OAuthServer\Test\Fixture\Model\Table\AuthCodesTable;

use Cake\I18n\FrozenTime;
use OAuthServer\Test\Fixture\AuthCodesFixture as BaseFixture;

class AuthCodesFixture extends BaseFixture
{
    public function init()
    {
        parent::init();
        $this->records = [];
        $this->records[] = [
            'code' => 'expired_at_005959',
            'expires' => (new FrozenTime('2020-01-01 00:59:59'))->getTimestamp(),
            'client_id' => 'TEST',
            'user_id' => 'user1',
            'revoked' => false,
        ];
        $this->records[] = [
            'code' => 'expired_at_010000',
            'expires' => (new FrozenTime('2020-01-01 01:00:00'))->getTimestamp(),
            'client_id' => 'TEST',
            'user_id' => 'user1',
            'revoked' => false,
        ];
        $this->records[] = [
            'code' => 'expired_at_010001',
            'expires' => (new FrozenTime('2020-01-01 01:00:01'))->getTimestamp(),
            'client_id' => 'TEST',
            'user_id' => 'user1',
            'revoked' => false,
        ];
        $this->records[] = [
            'code' => 'revoked1',
            'expires' => (new FrozenTime('2020-01-01 01:00:02'))->getTimestamp(),
            'client_id' => 'TEST',
            'user_id' => 'user1',
            'revoked' => true,
        ];
        $this->records[] = [
            'code' => 'revoked2',
            'expires' => (new FrozenTime('2020-01-01 01:00:03'))->getTimestamp(),
            'client_id' => 'TEST',
            'user_id' => 'user2',
            'revoked' => true,
        ];
    }
}
