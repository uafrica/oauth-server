<?php

namespace OAuthServer\Test\TestCase\Model\Table;

use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use OAuthServer\Model\Table\AccessTokensTable;
use OAuthServer\Model\Table\RefreshTokensTable;

class RefreshTokensTableTest extends TestCase
{
    public $fixtures = [
        'plugin.OAuthServer.Model\Table\RefreshTokensTable\RefreshTokens',
        'plugin.OAuthServer.Model\Table\RefreshTokensTable\AccessTokens',
        'plugin.OAuthServer.AccessTokenScopes',
        'plugin.OAuthServer.Clients',
        'plugin.OAuthServer.Scopes',
        'plugin.OAuthServer.Users',
    ];

    /**
     * @var RefreshTokensTable
     */
    private $RefreshTokens;

    /**
     * @var AccessTokensTable
     */
    private $AccessTokens;

    public function setUp()
    {
        parent::setUp();
        $this->RefreshTokens = TableRegistry::getTableLocator()->get('OAuthServer.RefreshTokens');
        $this->AccessTokens = TableRegistry::getTableLocator()->get('OAuthServer.AccessTokens');
        FrozenTime::setTestNow('2020-01-01 01:00:00');
    }

    public function tearDown()
    {
        unset($this->RefreshTokens, $this->AccessTokens);
        FrozenTime::setTestNow();
        parent::tearDown();
    }

    public function testFindExpiredToken()
    {
        $results = $this->RefreshTokens->find('Expired')->all();

        $this->assertSame(['expired_at_005959'], $results->extract('refresh_token')->toArray());
    }

    public function testFindRevokedToken()
    {
        $results = $this->RefreshTokens->find('Revoked')->all();

        $this->assertSame(['revoked1', 'revoked2'], $results->extract('refresh_token')->toArray());
    }

    public function testDropToken()
    {
        $results = $this->RefreshTokens
            ->find('Expired')
            ->union($this->RefreshTokens->find('Revoked'))
            ->all();

        $targetTokens = $results->extract('refresh_token')->toArray();
        $this->assertSame([
            'expired_at_005959',
            'revoked1',
            'revoked2',
        ], $targetTokens);
        foreach ($results as $entity) {
            $this->RefreshTokens->deleteOrFail($entity);
        }

        $oauthTokens = $results->extract('oauth_token')->toArray();
        $this->assertTrue($this->AccessTokens->exists(['oauth_token IN' => $oauthTokens]));
    }
}
