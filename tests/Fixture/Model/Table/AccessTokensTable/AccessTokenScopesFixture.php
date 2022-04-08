<?php

namespace OAuthServer\Test\Fixture\Model\Table\AccessTokensTable;

use OAuthServer\Test\Fixture\AccessTokenScopesFixture as BaseFixture;

class AccessTokenScopesFixture extends BaseFixture
{
    public function init()
    {
        parent::init();
        $this->records = [];
        $this->records[] = [
            'oauth_token' => 'expired_at_010000',
            'scope_id' => 'test',
        ];
        $this->records[] = [
            'oauth_token' => 'expired_at_010000',
            'scope_id' => 'awesome',
        ];
        $this->records[] = [
            'oauth_token' => 'revoked1',
            'scope_id' => 'test',
        ];
        $this->records[] = [
            'oauth_token' => 'revoked2',
            'scope_id' => 'test',
        ];
    }
}
