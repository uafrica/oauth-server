<?php

namespace OAuthServer\Test\TestCase\Model\Table;

use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use OAuthServer\Model\Table\AccessTokensTable;
use OAuthServer\Model\Table\RefreshTokensTable;

class AccessTokensTableTest extends TestCase
{
    public $fixtures = [
        'plugin.OAuthServer.Model\Table\AccessTokensTable\AccessTokens',
        'plugin.OAuthServer.Model\Table\AccessTokensTable\AccessTokenScopes',
        'plugin.OAuthServer.Model\Table\AccessTokensTable\RefreshTokens',
        'plugin.OAuthServer.Clients',
        'plugin.OAuthServer.Scopes',
        'plugin.OAuthServer.Users',
    ];

    /**
     * @var AccessTokensTable
     */
    private $AccessTokens;

    /**
     * @var RefreshTokensTable
     */
    private $RefreshTokens;

    public function setUp()
    {
        parent::setUp();
        $this->AccessTokens = TableRegistry::getTableLocator()->get('OAuthServer.AccessTokens');
        $this->RefreshTokens = TableRegistry::getTableLocator()->get('OAuthServer.RefreshTokens');
        FrozenTime::setTestNow('2020-01-01 01:00:00');
    }

    public function tearDown()
    {
        unset($this->AccessTokens, $this->RefreshTokens);
        FrozenTime::setTestNow();
        parent::tearDown();
    }

    public function testFindExpiredToken()
    {
        $results = $this->AccessTokens->find('Expired')->all();

        $this->assertSame(['expired_at_005959'], $results->extract('oauth_token')->toArray());
    }

    public function testFindRevokedToken()
    {
        $results = $this->AccessTokens->find('Revoked')->all();

        $this->assertSame(['revoked1', 'revoked2'], $results->extract('oauth_token')->toArray());
    }

    public function testDropToken()
    {
        $results = $this->AccessTokens
            ->find('Expired')
            ->union($this->AccessTokens->find('Revoked'))
            ->all();

        $targetTokens = $results->extract('oauth_token')->toArray();
        $this->assertSame([
            'expired_at_005959',
            'revoked1',
            'revoked2',
        ], $targetTokens);
        foreach ($results as $entity) {
            $this->AccessTokens->deleteOrFail($entity);
        }

        $this->assertFalse($this->AccessTokens->AccessTokenScopes->exists(['oauth_token IN' => $targetTokens]));
        $this->assertTrue($this->RefreshTokens->exists(['oauth_token IN' => $targetTokens]));
    }
}
