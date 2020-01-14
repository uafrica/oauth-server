<?php

namespace OAuthServer\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use OAuthServer\Model\Table\OauthScopesTable;

class OauthScopesTableTest extends TestCase
{
    public $fixtures = [
        'plugin.OAuthServer.Scopes',
    ];

    /**
     * @var OauthScopesTable
     */
    private $Scopes;

    public function setUp()
    {
        parent::setUp();
        $this->Scopes = TableRegistry::getTableLocator()->get('OAuthServer.OauthScopes');
    }

    public function tearDown()
    {
        unset($this->Scopes);
        parent::tearDown();
    }

    public function testInitialize()
    {
        $this->assertSame('oauth_scopes', $this->Scopes->getTable());
    }
}
