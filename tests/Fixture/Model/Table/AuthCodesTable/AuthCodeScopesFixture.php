<?php

namespace OAuthServer\Test\Fixture\Model\Table\AuthCodesTable;

use OAuthServer\Test\Fixture\AuthCodeScopesFixture as BaseFixture;

class AuthCodeScopesFixture extends BaseFixture
{
    public function init()
    {
        parent::init();
        $this->records = [];
        $this->records[] = [
            'auth_code' => 'expired_at_010000',
            'scope_id' => 'test',
        ];
        $this->records[] = [
            'auth_code' => 'expired_at_010000',
            'scope_id' => 'awesome',
        ];
        $this->records[] = [
            'auth_code' => 'revoked1',
            'scope_id' => 'test',
        ];
        $this->records[] = [
            'auth_code' => 'revoked2',
            'scope_id' => 'test',
        ];
    }
}
