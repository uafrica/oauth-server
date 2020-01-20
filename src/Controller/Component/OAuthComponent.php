<?php

namespace OAuthServer\Controller\Component;

use Cake\Auth\BaseAuthenticate;
use Cake\Controller\Component;
use Cake\Datasource\EntityInterface;
use Cake\Network\Exception\NotImplementedException;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use DateInterval;
use InvalidArgumentException;
use League\OAuth2\Server\AuthorizationServer;
use OAuthServer\Bridge\AuthorizationServerFactory;
use OAuthServer\Bridge\GrantFactory;
use OAuthServer\Bridge\UserFinderByUserCredentialsInterface;

class OAuthComponent extends Component implements UserFinderByUserCredentialsInterface
{
    /**
     * @var AuthorizationServer
     */
    protected $Server;

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
        'supportedGrants' => [
            'AuthCode',
            'RefreshToken',
            'ClientCredentials',
            'Password',
        ],
        'passwordAuthenticator' => 'Form',
        'privateKey' => null,
        'encryptionKey' => null,
        'accessTokenTTL' => 'PT1H',
        'refreshTokenTTL' => 'P1M',
        'authCodeTTL' => 'PT10M',
    ];

    /**
     * @param array $config Config array
     * @return void
     */
    public function initialize(array $config): void
    {
        if ($this->getConfig('server') && $this->getConfig('server') instanceof AuthorizationServer) {
            $this->setServer($this->getConfig('server'));
        }

        // setup enabled grant types.
        $server = $this->getServer();
        $supportedGrants = $this->getConfig('supportedGrants');
        $supportedGrants = $this->_registry->normalizeArray($supportedGrants);

        $grantFactory = new GrantFactory($this);

        foreach ($supportedGrants as $properties) {
            $grant = $properties['class'];

            if (!in_array($grant, $this->_allowedGrants)) {
                throw new NotImplementedException(__('The {0} grant type is not supported by the OAuthServer'));
            }

            $objGrant = $grantFactory->create($grant);

            if (method_exists($objGrant, 'setRefreshTokenTTL')) {
                $objGrant->setRefreshTokenTTL(new DateInterval($this->getConfig('refreshTokenTTL')));
            }

            foreach ($properties['config'] as $key => $value) {
                $method = 'set' . Inflector::camelize($key);
                if (is_callable([$objGrant, $method])) {
                    $objGrant->$method($value);
                }
            }

            $server->enableGrantType($objGrant, new DateInterval($this->getConfig('accessTokenTTL')));
        }
    }

    /**
     * @return AuthorizationServer
     */
    public function getServer(): AuthorizationServer
    {
        if (!$this->Server) {
            $factory = new AuthorizationServerFactory(
                $this->getConfig('privateKey'),
                $this->getConfig('encryptionKey')
            );

            $this->setServer($factory->create());
        }

        return $this->Server;
    }

    /**
     * @param AuthorizationServer $Server a AuthorizationServer instance.
     * @return void
     */
    public function setServer(AuthorizationServer $Server): void
    {
        $this->Server = $Server;
    }

    /**
     * {@inheritDoc}
     */
    public function findUser($username, $password): ?EntityInterface
    {
        $controller = $this->_registry->getController();
        $auth = $this->getPasswordAuthenticator();

        $request = $controller->request
            ->withData($auth->getConfig('fields.username'), $username)
            ->withData($auth->getConfig('fields.password'), $password);

        $user = $auth->authenticate($request, $controller->response);

        if ($user === false) {
            return null;
        }

        return new Entity($user);
    }

    /**
     * {@inheritDoc}
     */
    public function getPrimaryKey()
    {
        return $this->getUserModel()->getPrimaryKey();
    }

    /**
     * @return BaseAuthenticate
     */
    protected function getPasswordAuthenticator(): BaseAuthenticate
    {
        $controller = $this->_registry->getController();

        if (!$controller->Auth) {
            throw new InvalidArgumentException(__('OAuthComponent require AuthComponent.'));
        }

        $controller->Auth->constructAuthenticate();
        $auth = $controller->Auth->getAuthenticate($this->getConfig('passwordAuthenticator'));

        if ($auth === null) {
            throw new InvalidArgumentException(__('Can\'t get PasswordAuthenticator.'));
        }

        return $auth;
    }

    /**
     * @return Table
     */
    protected function getUserModel(): Table
    {
        $auth = $this->getPasswordAuthenticator();
        $userModel = $auth->getConfig('userModel');

        if ($userModel === null) {
            throw new InvalidArgumentException(__('UserModel not set.'));
        }

        return TableRegistry::getTableLocator()->get($userModel);
    }
}
