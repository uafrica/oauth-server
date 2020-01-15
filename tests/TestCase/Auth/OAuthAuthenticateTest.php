<?php

namespace OAuthServer\Test\TestCase\Auth;

use Cake\Controller\ComponentRegistry;
use Cake\Core\Plugin;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use League\OAuth2\Server\CryptKey;
use OAuthServer\Auth\OAuthAuthenticate;
use OAuthServer\Model\Entity\AccessToken;
use OAuthServer\Model\Entity\Client;
use PHPUnit\Framework\MockObject\MockObject;

class OAuthAuthenticateTest extends TestCase
{
    public $fixtures = [
        'plugin.OAuthServer.Clients',
        'plugin.OAuthServer.Scopes',
        'plugin.OAuthServer.AccessTokens',
        'plugin.OAuthServer.Users',
    ];

    /**
     * @var ComponentRegistry|MockObject|mixed
     */
    private $Collection;

    /**
     * @var OAuthAuthenticate
     */
    private $auth;

    /**
     * @var Response|MockObject
     */
    private $response;

    /**
     * @var string
     */
    private $privateKeyPath;

    /**
     * @var string
     */
    private $publicKeyPath;

    /**
     * setup
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->privateKeyPath = Plugin::path('OAuthServer') . 'tests/Fixture/test.pem';
        $this->publicKeyPath = Plugin::path('OAuthServer') . 'tests/Fixture/test-pub.pem';

        $this->Collection = $this->getMockBuilder(ComponentRegistry::class)->getMock();
        $this->response = $this->getMockBuilder(Response::class)->getMock();

        $this->auth = new OAuthAuthenticate($this->Collection, [
            'userModel' => 'Users',
            'publicKey' => $this->publicKeyPath,
        ]);

        FrozenTime::setTestNow();
        TableRegistry::clear();
    }

    public function testAuthenticate()
    {
        $client = new Client();
        $client->id = 'TEST';
        $accessToken = new AccessToken();
        $accessToken->setIdentifier('exist_token_1');
        $accessToken->setUserIdentifier('user1');
        $accessToken->setExpiryDateTime(FrozenTime::now()->addHour());
        $accessToken->setClient($client);
        $accessToken->setPrivateKey(new CryptKey('file://' . $this->privateKeyPath));
        $token = (string)$accessToken;

        $request = (new ServerRequest())->withHeader('authorization', \sprintf('Bearer %s', $token));

        $this->assertNotFalse($this->auth->authenticate($request, $this->response));
    }

    public function testAuthenticateWithoutBear()
    {
        $request = new ServerRequest('posts/index');

        $this->assertFalse($this->auth->authenticate($request, $this->response));
    }
}
