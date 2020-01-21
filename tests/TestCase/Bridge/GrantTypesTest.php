<?php

namespace OAuthServer\Test\TestCase\Bridge;

use OAuthServer\Bridge\GrantTypes;
use PHPUnit\Framework\TestCase;

class GrantTypesTest extends TestCase
{
    public function testGetAllowedGrantTypes()
    {
        $this->assertSame([
            'client_credentials',
            'authorization_code',
            'refresh_token',
            'password',
        ], GrantTypes::getAllowedGrantTypes());
    }

    public function testConvertFromGrantClassName()
    {
        $this->assertSame('authorization_code', GrantTypes::convertFromGrantClassName('AuthCodeGrant'));
    }
}
