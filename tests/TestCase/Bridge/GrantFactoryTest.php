<?php

namespace OAuthServer\Test\TestCase\Bridge;

use Cake\TestSuite\TestCase;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use OAuthServer\Bridge\GrantFactory;
use OAuthServer\Bridge\UserFinderByUserCredentialsInterface;

class GrantFactoryTest extends TestCase
{
    /**
     * @dataProvider dataCreate
     * @param string $grantType grant type
     * @param string $expects grant class
     */
    public function testCreate($grantType, $expects)
    {
        $factory = new GrantFactory($this->getMockBuilder(UserFinderByUserCredentialsInterface::class)->getMock());

        $this->assertInstanceOf($expects, $factory->create($grantType));
    }

    public function dataCreate()
    {
        return [
            ['ClientCredentials', ClientCredentialsGrant::class],
            ['Password', PasswordGrant::class],
            ['AuthCode', AuthCodeGrant::class],
            ['RefreshToken', RefreshTokenGrant::class],
        ];
    }
}
