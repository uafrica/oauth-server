<?php

namespace OAuthServer\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use OAuthServer\Model\Table\OauthClientsTable;

class OauthClientsTableTest extends TestCase
{
    public $fixtures = [
        'plugin.OAuthServer.Clients',
    ];

    /**
     * @var OauthClientsTable
     */
    private $Clients;

    public function setUp()
    {
        parent::setUp();
        $this->Clients = TableRegistry::getTableLocator()->get('OAuthServer.OauthClients');
    }

    public function tearDown()
    {
        unset($this->Clients);
        parent::tearDown();
    }

    public function testGenerateClientId()
    {
        $this->assertSame(20, strlen($this->Clients->generateClientId()));
        $this->assertRegExp('!\A[A-z0-9]+\z!', $this->Clients->generateClientId());
    }

    public function testGenerateSecret()
    {
        $this->assertSame(40, strlen($this->Clients->generateSecret()));
        $this->assertRegExp('!\A[A-z0-9]+\z!', $this->Clients->generateSecret());
    }

    public function testGenerateIdAndSecretOnCreate()
    {
        $entity = $this->Clients->newEntity(['name' => 'NewClient', 'redirect_uri' => ['https://example.com']]);

        $this->assertNull($entity->id);
        $this->assertNull($entity->client_secret);

        $this->Clients->saveOrFail($entity);

        $this->assertSame(20, strlen($entity->id));
        $this->assertSame(40, strlen($entity->client_secret));
    }

    public function testRedirectUriIsJsonType()
    {
        $entity = $this->Clients->newEntity(['name' => 'NewClient', 'redirect_uri' => ['https://example.com', 'https://example.co.jp']]);

        $this->assertSame(['https://example.com', 'https://example.co.jp'], $entity->redirect_uri);
    }

    public function testGrantTypesIsJsonType()
    {
        $entity = $this->Clients->newEntity([
            'name' => 'NewClient',
            'redirect_uri' => ['https://example.com', 'https://example.co.jp'],
            'grant_types' => ['client_credentials', 'refresh_token'],
        ]);

        $this->assertSame(['client_credentials', 'refresh_token'], $entity->grant_types);
    }
}
