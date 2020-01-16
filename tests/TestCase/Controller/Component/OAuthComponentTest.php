<?php

namespace OAuthServer\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Component\AuthComponent;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use League\OAuth2\Server\AuthorizationServer;
use OAuthServer\Controller\Component\OAuthComponent;

class OAuthComponentTest extends TestCase
{
    public $fixtures = [
        'plugin.OAuthServer.Clients',
        'plugin.OAuthServer.Scopes',
        'plugin.OAuthServer.AccessTokens',
        'plugin.OAuthServer.AccessTokenScopes',
        'plugin.OAuthServer.AuthCodes',
        'plugin.OAuthServer.AuthCodeScopes',
        'plugin.OAuthServer.RefreshTokens',
        'plugin.OAuthServer.Users',
    ];

    /**
     * @var ComponentRegistry
     */
    private $componentRegistry;

    /**
     * @var OAuthComponent
     */
    private $component;

    /**
     * @var Controller|\PHPUnit\Framework\MockObject\MockObject
     */
    private $controller;

    public function setUp()
    {
        parent::setUp();
        $this->controller = new Controller(new ServerRequest(), new Response());
        $this->componentRegistry = new ComponentRegistry($this->controller);
        $this->controller->Auth = new AuthComponent($this->componentRegistry, [
            'authenticate' => [
                AuthComponent::ALL => [
                    'userModel' => 'Users',
                    'fields' => [
                        'username' => 'email',
                    ],
                ],
                'Form',
            ],
        ]);

        $this->component = new OAuthComponent($this->componentRegistry, Configure::read('OAuthServer', []));
    }

    public function tearDown()
    {
        unset($this->component, $this->componentRegistry);
        parent::tearDown();
    }

    public function testInitialize()
    {
        $this->assertInstanceOf(AuthorizationServer::class, $this->component->getServer());
    }

    public function testGetPrimaryKey()
    {
        $this->assertSame('id', $this->component->getPrimaryKey());
    }

    public function testFindUser()
    {
        $result = $this->component->findUser('user1@example.com', '123456');
        $this->assertSame('user1', $result->get('id'));

        $result = $this->component->findUser('user2@example.com', '654321');
        $this->assertSame('user2', $result->get('id'));

        $this->assertNull($this->component->findUser('user1@example.com', '654321'));
    }
}
