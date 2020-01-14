<?php
namespace OAuthServer\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Controller\ComponentRegistry;
use Cake\Core\App;
use Cake\Database\Exception;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\HttpException;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\ORM\TableRegistry;
use League\OAuth2\Server\Exception\OAuthException;
use OAuthServer\Traits\GetStorageTrait;

class OAuthAuthenticate extends BaseAuthenticate
{
    use GetStorageTrait;

    /**
     * @var \League\OAuth2\Server\ResourceServer
     */
    public $Server;

    /**
     * Exception that was thrown by oauth server
     *
     * @var \League\OAuth2\Server\Exception\OAuthException
     */
    protected $_exception;

    /**
     * @var array
     */
    protected $_defaultConfig = [
        'continue' => false,
        'storages' => [
            'session' => [
                'className' => 'OAuthServer.Session',
            ],
            'accessToken' => [
                'className' => 'OAuthServer.AccessToken',
            ],
            'client' => [
                'className' => 'OAuthServer.Client',
            ],
            'scope' => [
                'className' => 'OAuthServer.Scope',
            ],
        ],
        'resourceServer' => [
            'className' => 'League\OAuth2\Server\ResourceServer',
        ],
        'contain' => null,
    ];

    /**
     * @param \Cake\Controller\ComponentRegistry $registry Component registry
     * @param array $config Config array
     */
    public function __construct(ComponentRegistry $registry, $config)
    {
        parent::__construct($registry, $config);

        if ($this->getConfig('server')) {
            $this->Server = $this->getConfig('server');

            return;
        }

        $serverConfig = $this->getConfig('resourceServer');
        $serverClassName = App::className($serverConfig['className']);

        if (!$serverClassName) {
            throw new Exception('ResourceServer class was not found.');
        }

        $server = new $serverClassName(
            $this->_getStorage('session'),
            $this->_getStorage('accessToken'),
            $this->_getStorage('client'),
            $this->_getStorage('scope')
        );

        $this->Server = $server;
    }

    /**
     * Authenticate a user based on the request information.
     *
     * @param \Cake\Network\Request $request Request to get authentication information from.
     * @param \Cake\Network\Response $response A response object that can have headers added.
     * @return bool
     */
    public function authenticate(Request $request, Response $response)
    {
        return $this->getUser($request);
    }

    /**
     * @param \Cake\Network\Request $request Request to get authentication information from.
     * @param \Cake\Network\Response $response A response object that can have headers added.
     * @return bool|\Cake\Network\Response
     */
    public function unauthenticated(Request $request, Response $response)
    {
        if ($this->_config['continue']) {
            return false;
        }
        if (isset($this->_exception)) {
            // ignoring $e->getHttpHeaders() for now
            // it only sends WWW-Authenticate header in case of InvalidClientException
            throw new HttpException($this->_exception->getMessage(), $this->_exception->httpStatusCode, $this->_exception);
        }
        $message = __d('authenticate', 'You are not authenticated.');
        throw new BadRequestException($message);
    }

    /**
     * @param \Cake\Network\Request $request Request object
     * @return array|bool|mixed
     */
    public function getUser(Request $request)
    {
        try {
            $this->Server->isValidRequest(true, $request->query('access_token'));
        } catch (OAuthException $e) {
            $this->_exception = $e;

            return false;
        }

        $ownerModel = $this->Server
            ->getAccessToken()
            ->getSession()
            ->getOwnerType();

        $ownerId = $this->Server
            ->getAccessToken()
            ->getSession()
            ->getOwnerId();

        $options = [];

        if ($this->_config['contain']) {
            $options['contain'] = $this->_config['contain'];
        }

        $owner = TableRegistry::get($ownerModel)
            ->get($ownerId, $options)
            ->toArray();

        $event = new Event('OAuthServer.getUser', $request, [$ownerModel, $ownerId, $owner]);
        EventManager::instance()->dispatch($event);

        if ($event->result !== null) {
            return $event->result;
        } else {
            return $owner;
        }
    }
}
