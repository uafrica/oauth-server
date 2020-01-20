<?php

namespace OAuthServer\Test\TestCase\Model\Table;

use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use OAuthServer\Model\Table\AuthCodesTable;

class AuthCodesTableTest extends TestCase
{
    public $fixtures = [
        'plugin.OAuthServer.Model\Table\AuthCodesTable\AuthCodes',
        'plugin.OAuthServer.Model\Table\AuthCodesTable\AuthCodeScopes',
        'plugin.OAuthServer.Clients',
        'plugin.OAuthServer.Scopes',
        'plugin.OAuthServer.Users',
    ];

    /**
     * @var AuthCodesTable
     */
    private $AuthCodes;

    public function setUp()
    {
        parent::setUp();
        $this->AuthCodes = TableRegistry::getTableLocator()->get('OAuthServer.AuthCodes');
        FrozenTime::setTestNow('2020-01-01 01:00:00');
    }

    public function tearDown()
    {
        unset($this->AuthCodes);
        FrozenTime::setTestNow();
        parent::tearDown();
    }

    public function testFindExpiredToken()
    {
        $results = $this->AuthCodes->find('Expired')->all();

        $this->assertSame(['expired_at_005959'], $results->extract('code')->toArray());
    }

    public function testFindRevokedToken()
    {
        $results = $this->AuthCodes->find('Revoked')->all();

        $this->assertSame(['revoked1', 'revoked2'], $results->extract('code')->toArray());
    }

    public function testDropToken()
    {
        $results = $this->AuthCodes
            ->find('Expired')
            ->union($this->AuthCodes->find('Revoked'))
            ->all();

        $targetTokens = $results->extract('code')->toArray();
        $this->assertSame([
            'expired_at_005959',
            'revoked1',
            'revoked2',
        ], $targetTokens);
        foreach ($results as $entity) {
            $this->AuthCodes->deleteOrFail($entity);
        }

        $this->assertFalse($this->AuthCodes->AuthCodeScopes->exists(['auth_code IN' => $targetTokens]));
    }
}
