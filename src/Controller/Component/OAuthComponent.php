<?php
namespace OAuthServer\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\App;
use Cake\Network\Exception\NotImplementedException;
use Cake\Utility\Inflector;
use Cake\Core\Configure;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use OAuthServer\Traits\GetStorageTrait;
use OAuthServer\Model\Entities\UserEntity;
use OAuthServer\Model\Repositories\AccessTokenRepository;
use OAuthServer\Model\Repositories\AuthCodeRepository;
use OAuthServer\Model\Repositories\ClientRepository;
use OAuthServer\Model\Repositories\RefreshTokenRepository;
use OAuthServer\Model\Repositories\ScopeRepository;

class OAuthComponent extends Component
{
    use GetStorageTrait;

    /**
     * @var \League\OAuth2\Server\AuthorizationServer
     */
    public $Server;

    /**
     * @var \League\OAuth2\Server\ResourceServer
     */
    public $ResourceServer;


    /**
     * Grant types currently supported by the plugin
     *
     * @var array
     */
    protected $_allowedGrants = ['AuthCode', 'RefreshToken', 'ClientCredentials', 'Password'];

    /**
     * @var array
     */
    protected $_defaultConfig = [
        'supportedGrants' => ['AuthCode', 'RefreshToken', 'ClientCredentials', 'Password'],
        'storages' => [
            'session' => [
                'className' => 'OAuthServer.Session'
            ],
            'accessToken' => [
                'className' => 'OAuthServer.AccessToken'
            ],
            'client' => [
                'className' => 'OAuthServer.Client'
            ],
            'scope' => [
                'className' => 'OAuthServer.Scope'
            ],
            'authCode' => [
                'className' => 'OAuthServer.AuthCode'
            ],
            'refreshToken' => [
                'className' => 'OAuthServer.RefreshToken'
            ]
        ],
        'authorizationServer' => [
            'className' => 'League\OAuth2\Server\AuthorizationServer'
        ],
        'resourceServer' => [
            'className' => 'League\OAuth2\Server\ResourceServer'
        ]
    ];

    /**
     * @return \League\OAuth2\Server\AuthorizationServer
     */
    protected function _getAuthorizationServer(array $config)
    {
        $serverConfig = $this->config('authorizationServer');
        $serverClassName = App::className($serverConfig['className']);
        $clientRepository = new ClientRepository();
        $scopeRepository = new ScopeRepository();
        $accessTokenRepository = new AccessTokenRepository();
        $authCodeRepository = new AuthCodeRepository();
        $refreshTokenRepository = new RefreshTokenRepository();

        $privateKeyPath = 'file://' . ROOT . DS . 'config' . '/private.key';
        $encryptionKey = Configure::read('OAuth.encryptionKey');
        $server = new $serverClassName($clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $privateKeyPath,
            $encryptionKey
        );

        // Enable the authentication code grant on the server with a token TTL of 1 hour
        $server->enableGrantType(
            new AuthCodeGrant(
                $authCodeRepository,
                $refreshTokenRepository,
                new \DateInterval('PT10M')
            ),
            new \DateInterval('PT23H')
        );

        $grant = new RefreshTokenGrant($refreshTokenRepository);
        $grant->setRefreshTokenTTL(new \DateInterval('P2M')); // The refresh token will expire in 2 month

        $server->enableGrantType(
            $grant,
            new \DateInterval('PT23H') // The new access token will expire after 23 hour
        );

        $server->enableGrantType(
            new ClientCredentialsGrant(),
            new \DateInterval('PT1H') // access tokens will expire after 1 hour
        );

        return $server;
    }

    /**
     * @return \League\OAuth2\Server\ResourceServer
     */
    protected function _getResourceServer(array $config)
    {
        $serverConfig = $this->config('resourceServer');
        $serverClassName = App::className($serverConfig['className']);
        $publicKeyPath = 'file://' . ROOT . DS . 'config' . '/public.key';
        $encryptionKey = Configure::read('OAuth.encryptionKey');
        $accessTokenRepository = new AccessTokenRepository();
        $server = new $serverClassName(
            $accessTokenRepository,
            $publicKeyPath
        );

        return $server;
    }

    /**
     * @param array $config Config array
     * @return void
     */
    public function initialize(array $config)
    {
        $server = $this->_getAuthorizationServer($config);
        $resourceServer = $this->_getResourceServer($config);
        $this->Server = $server;
        $this->ResourceServer = $resourceServer;
    }
}
