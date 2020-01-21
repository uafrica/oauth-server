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
        $this->Clients = TableRegistry::getTableLocator()->get('OAuthServer.OauthClients');
    }

    public function tearDown()
    {
        unset($this->Clients);
        parent::tearDown();
    }

    /**
     * @dataProvider dataValidateRedirectUri
     */
    public function testValidateRedirectUri($uri, $expects)
    {
        $entity = $this->Clients->newEntity([
            'redirect_uri' => $uri,
        ]);

        $this->assertSame($expects, $entity->getError('redirect_uri'));
    }

    public function dataValidateRedirectUri()
    {
        return [
            'valid: one valid uri' => [
                ['http://example.com/callback'],
                [],
            ],
            'valid: one valid url scheme as app' => [
                ['com.example.app://callback'],
                [],
            ],
            'valid: two valid uri' => [
                ['http://example.com/callback', 'https://example.co.jp/auth'],
                [],
            ],
            'invalid: one invalid uri' => [
                ['http://example/callback'],
                ['url' => 'the redirect_uri contains invalid uri.'],
            ],
            'invalid: one valid uri, one invalid uri' => [
                ['http://example/callback', 'https://example.co.jp/auth'],
                ['url' => 'the redirect_uri contains invalid uri.'],
            ],
            'invalid: empty uri' => [
                [''],
                ['url' => 'the redirect_uri contains invalid uri.'],
            ],
        ];
    }

    /**
     * @dataProvider dataValidateGrantTypes
     */
    public function testValidateGrantTypes($grantTypes, $expects)
    {
        $entity = $this->Clients->newEntity([
            'grant_types' => $grantTypes,
        ]);

        $this->assertSame($expects, $entity->getError('grant_types'));
    }

    public function dataValidateGrantTypes()
    {
        return [
            'valid: all valid grant_types' => [
                ['client_credentials', 'authorization_code', 'refresh_token', 'password'],
                [],
            ],
            'valid: one valid grant_types' => [
                ['client_credentials'],
                [],
            ],
            'valid: allows empty' => [
                [],
                [],
            ],
            'invalid: one invalid grant_types' => [
                ['invalid'],
                ['allowed' => 'the grant_types contains invalid grant type.'],
            ],
            'invalid: one valid grant_types, one invalid grant_types' => [
                ['client_credentials', 'invalid'],
                ['allowed' => 'the grant_types contains invalid grant type.'],
            ],
        ];
    }
}
