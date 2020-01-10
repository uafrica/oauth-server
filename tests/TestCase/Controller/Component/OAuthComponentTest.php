<?php

namespace OAuthServer\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use OAuthServer\Controller\Component\OAuthComponent;

class OAuthComponentTest extends TestCase
{
    public function testDefaultTokenTTL()
    {
        $component = new OAuthComponent(new ComponentRegistry(), []);
        $this->assertEquals(3600, $component->Server->getAccessTokenTTL());
    }

    public function testConfigTokenTTL()
    {
        $component = new OAuthComponent(new ComponentRegistry(), [
            'accessTokenTTL' => 5,
        ]);
        $this->assertEquals(5, $component->Server->getAccessTokenTTL());
    }

    /**
     * @expectedException \League\OAuth2\Server\Exception\InvalidGrantException
     */
    public function testGrantWhitelist()
    {
        $component = new OAuthComponent(new ComponentRegistry(), [
            'supportedGrants' => ['AuthCode'],
        ]);
        $component->Server->getGrantType('refresh_token');
    }

    public function testGrantConfig()
    {
        $component = new OAuthComponent(new ComponentRegistry(), [
            'supportedGrants' => [
                'RefreshToken' => [
                    'refreshTokenTTL' => 4,
                ],
            ],
        ]);

        /** @var RefreshTokenGrant $grant */
        $grant = $component->Server->getGrantType('refresh_token');
        $this->assertEquals(4, $grant->getRefreshTokenTTL());
    }
}
