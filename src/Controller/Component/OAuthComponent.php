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
            new \DateInterval('PT1H')
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
        // debug($server);exit;
        // $server->setSessionStorage($this->_getStorage('session'));
        // $server->setAccessTokenStorage($this->_getStorage('accessToken'));
        // $server->setClientStorage($this->_getStorage('client'));
        // $server->setScopeStorage($this->_getStorage('scope'));
        // $server->setAuthCodeStorage($this->_getStorage('authCode'));
        // $server->setRefreshTokenStorage($this->_getStorage('refreshToken'));

        // $supportedGrants = isset($config['supportedGrants']) ? $config['supportedGrants'] : $this->config('supportedGrants');
        // $supportedGrants = $this->_registry->normalizeArray($supportedGrants);
        //
        // foreach ($supportedGrants as $properties) {
        //     $grant = $properties['class'];
        //
        //     if (!in_array($grant, $this->_allowedGrants)) {
        //         throw new NotImplementedException(__('The {0} grant type is not supported by the OAuthServer'));
        //     }
        //
        //     $className = '\\League\\OAuth2\\Server\\Grant\\' . $grant . 'Grant';
        //     $objGrant = new $className();
        //
        //     if ($grant === 'Password') {
        //         $objGrant->setVerifyCredentialsCallback(function ($username, $password) {
        //             $controller = $this->_registry->getController();
        //             $controller->Auth->constructAuthenticate();
        //             $userfield = $controller->Auth->_config['authenticate']['Form']['fields']['username'];
        //             $controller->request->data[$userfield] = $username;
        //             $controller->request->data['password'] = $password;
        //             $loginOk = $controller->Auth->identify();
        //             if ($loginOk) {
        //                 return $loginOk['id'];
        //             } else {
        //                 return false;
        //             }
        //         });
        //     }
        //
        //     foreach ($properties['config'] as $key => $value) {
        //         $method = 'set' . Inflector::camelize($key);
        //         if (is_callable([$objGrant, $method])) {
        //             $objGrant->$method($value);
        //         }
        //     }
        //
        //     $server->addGrantType($objGrant);
        // }
        //
        // if ($this->config('accessTokenTTL')) {
        //     $server->setAccessTokenTTL($this->config('accessTokenTTL'));
        // }

        $this->Server = $server;
        $this->ResourceServer = $resourceServer;
    }
}
