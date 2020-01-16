<?php

namespace OAuthServer\Test\TestCase\Controller;

use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestCase;
use Defuse\Crypto\Key;
use League\OAuth2\Server\CryptTrait;
use OAuthServer\Auth\OAuthAuthenticate;
use OAuthServer\Model\Table\AccessTokensTable;
use OAuthServer\Model\Table\AuthCodesTable;
use OAuthServer\Model\Table\RefreshTokensTable;

class IntegrationTest extends IntegrationTestCase
{
    use CryptTrait;

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
     * @var AccessTokensTable
     */
    private $AccessTokens;

    /**
     * @var RefreshTokensTable
     */
    private $RefreshTokens;

    /**
     * @var AuthCodesTable
     */
    private $AuthCodes;

    /**
     * @var OAuthAuthenticate
     */
    private $auth;

    /**
     * @noinspection PhpIncludeInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function setUp()
    {
        // class Router needs to be loaded in order for TestCase to automatically include routes
        // not really sure how to do it properly, this hotfix seems good enough
        Router::defaultRouteClass();

        parent::setUp();

        Router::connect('/');
        Router::scope('/', static function (RouteBuilder $route) {
            $route->fallbacks();
        });
        include Plugin::configPath('OAuthServer') . 'routes.php';

        $this->AccessTokens = TableRegistry::getTableLocator()->get('OAuthServer.AccessTokens');
        $this->RefreshTokens = TableRegistry::getTableLocator()->get('OAuthServer.RefreshTokens');
        $this->AuthCodes = TableRegistry::getTableLocator()->get('OAuthServer.AuthCodes');

        $this->setEncryptionKey(Key::loadFromAsciiSafeString(Configure::read('OAuthServer.encryptionKey')));

        $componentRegistry = $this->getMockBuilder(ComponentRegistry::class)->getMock();

        $this->auth = new OAuthAuthenticate($componentRegistry, [
            'userModel' => 'Users',
            'publicKey' => Configure::read('OAuthServer.publicKey'),
        ]);
    }

    public function tearDown()
    {
        unset($this->AccessTokens, $this->RefreshTokens, $this->AuthCodes);
        parent::tearDown();
    }

    public function testAuthorization()
    {
        // --- 1. Get a authorization code
        $this->session(['Auth.User.id' => 'user1']);

        $query = ['client_id' => 'TEST', 'redirect_uri' => 'http://www.example.com', 'response_type' => 'code', 'scope' => 'test'];
        $this->post($this->url('/oauth/authorize') . '?' . http_build_query($query), ['authorization' => 'Approve']);

        $this->assertRedirect();

        $redirectUrl = $this->_response->getHeaderLine('Location');
        $this->assertStringStartsWith('http://www.example.com?code=', $redirectUrl);
        parse_str(parse_url($redirectUrl, PHP_URL_QUERY), $responseQuery);

        // check authorization code stored in the database.
        $codeDecrypted = json_decode($this->decrypt($responseQuery['code']), true);
        $this->assertTrue($this->AuthCodes->exists(['code' => $codeDecrypted['auth_code_id']]), 'generated auth code exists');

        // --- 2. Logged out and get access token with authorization code.
        $this->session(['Auth.User.id' => null]);

        $this->post('/oauth/access_token', [
            'grant_type' => 'authorization_code',
            'client_id' => 'TEST',
            'client_secret' => 'TestSecret',
            'redirect_uri' => 'http://www.example.com',
            'code' => $responseQuery['code'],
        ]);
        $this->assertResponseOk();

        $response = $this->grabResponseJson();
        $this->assertSame('Bearer', $response['token_type']);
        $this->assertSame(3600, $response['expires_in']);
        $this->assertArrayHasKey('access_token', $response);
        $this->assertArrayHasKey('refresh_token', $response);

        // check revoked authorization code
        $this->assertTrue($this->AuthCodes->exists(['code' => $codeDecrypted['auth_code_id'], 'revoked' => true]), 'revoked auth code');

        // check token stored in the database.
        $tokenDecrypted = json_decode($this->decrypt($response['refresh_token']), true);
        $this->assertTrue($this->AccessTokens->exists([
            'oauth_token' => $tokenDecrypted['access_token_id'],
        ]), 'generated access token exists');
        $this->assertTrue($this->RefreshTokens->exists([
            'refresh_token' => $tokenDecrypted['refresh_token_id'],
            'oauth_token' => $tokenDecrypted['access_token_id'],
        ]), 'generated refresh token exists');

        // ----- 3. Refresh Token
        $this->post('/oauth/access_token', [
            'grant_type' => 'refresh_token',
            'client_id' => 'TEST',
            'client_secret' => 'TestSecret',
            'scope' => 'test',
            'refresh_token' => $response['refresh_token'],
        ]);
        $this->assertResponseOk();
        $refreshed = $this->grabResponseJson();
        $this->assertSame('Bearer', $refreshed['token_type']);
        $this->assertSame(3600, $refreshed['expires_in']);
        $this->assertArrayHasKey('access_token', $refreshed);
        $this->assertArrayHasKey('refresh_token', $refreshed);
        $this->assertNotEquals($response['access_token'], $refreshed['access_token']);
        $this->assertNotEquals($response['refresh_token'], $refreshed['refresh_token']);

        // check revoked previous token
        $this->assertTrue($this->AccessTokens->exists([
            'oauth_token' => $tokenDecrypted['access_token_id'],
            'revoked' => true,
        ]), 'revoked access token');
        $this->assertTrue($this->RefreshTokens->exists([
            'refresh_token' => $tokenDecrypted['refresh_token_id'],
            'oauth_token' => $tokenDecrypted['access_token_id'],
            'revoked' => true,
        ]), 'revoked refresh token');

        // check token stored in the database.
        $refreshedTokenDecrypted = json_decode($this->decrypt($refreshed['refresh_token']), true);
        $this->assertTrue($this->AccessTokens->exists([
            'oauth_token' => $refreshedTokenDecrypted['access_token_id'],
        ]), 'generated access token exists');
        $this->assertTrue($this->RefreshTokens->exists([
            'refresh_token' => $refreshedTokenDecrypted['refresh_token_id'],
            'oauth_token' => $refreshedTokenDecrypted['access_token_id'],
        ]), 'generated refresh token exists');

        // --- Test authorization with access token
        $request = (new ServerRequest())->withHeader('Authorization', sprintf('Bearer %s', $refreshed['access_token']));
        $authResult = $this->auth->authenticate($request, new Response());
        $this->assertSame('Alice', $authResult['name']);
        // Can't authorization with previous token
        $request = (new ServerRequest())->withHeader('Authorization', sprintf('Bearer %s', $response['access_token']));
        $this->assertFalse($this->auth->authenticate($request, new Response()));
    }

    private function grabResponseJson()
    {
        return json_decode((string)$this->_response->getBody(), true);
    }

    private function url($path, $ext = null)
    {
        $ext = $ext ? ".$ext" : '';

        return $path . $ext;
    }
}
