<?php
namespace OAuthServer\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\App;
use Cake\Network\Exception\NotImplementedException;
use OAuthServer\Model\Storage;
use OAuthServer\Traits\GetStorageTrait;

class OAuthComponent extends Component
{
    use GetStorageTrait;

    /**
     * @var \League\OAuth2\Server\AuthorizationServer
     */
    public $Server;

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
        'tokenTTL' => 2592000, //TTL 30 * 24 * 60 * 60 in seconds
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
        ]
    ];

    /**
     * @return \League\OAuth2\Server\AuthorizationServer
     */
    protected function _getAuthorizationServer()
    {
        $serverConfig = $this->config('authorizationServer');
        $serverClassName = App::className($serverConfig['className']);
        return new $serverClassName();
    }

    /**
     * @param array $config Config array
     * @return void
     */
    public function initialize(array $config)
    {
        $server = $this->_getAuthorizationServer();
        $server->setSessionStorage($this->_getStorage('session'));
        $server->setAccessTokenStorage($this->_getStorage('accessToken'));
        $server->setClientStorage($this->_getStorage('client'));
        $server->setScopeStorage($this->_getStorage('scope'));
        $server->setAuthCodeStorage($this->_getStorage('authCode'));
        $server->setRefreshTokenStorage($this->_getStorage('refreshToken'));

        $supportedGrants = isset($config['supportedGrants']) ? $config['supportedGrants'] : $this->config('supportedGrants');
        foreach ($supportedGrants as $grant) {
            if (!in_array($grant, $this->_allowedGrants)) {
                throw new NotImplementedException(__('The {0} grant type is not supported by the OAuth server'));
            }

            $className = '\\League\\OAuth2\\Server\\Grant\\' . $grant . 'Grant';
            $objGrant = new $className();
            if ($grant === 'Password') {
	            $objGrant->setVerifyCredentialsCallback(function ($username, $password) {    
	                $controller = $this->_registry->getController();;
	                $controller->Auth->constructAuthenticate();
	                $userfield = $controller->components['Auth']['authenticate']['Form']['fields']['username'];
	                $controller->request->data[$userfield] = $username;
	                $controller->request->data['password'] = $password;
	                $loginOk = $controller->Auth->identify();
	                if ($loginOk) {
	                	return true;
	                } else {
	                	return false;
	                }
	            });
            }
            $server->addGrantType($objGrant);
        }

        $server->setAccessTokenTTL($this->config('tokenTTL'));

        $this->Server = $server;
    }

    /**
     * @param string $authGrant Grant type
     * @return bool|\Cake\Network\Response|void
     */
    public function checkAuthParams($authGrant)
    {
        $controller = $this->_registry->getController();
        try {
            return $this->Server->getGrantType($authGrant)->checkAuthorizeParams();
        } catch (\OAuthException $e) {
            if ($e->shouldRedirect()) {
                return $controller->redirect($e->getRedirectUri());
            }

            $controller->RequestHandler->renderAs($this, 'json');
            $controller->response->statusCode($e->httpStatusCode);
            $controller->response->header($e->getHttpHeaders());
            $controller->set('response', $e);
            return false;
        }
    }
}
