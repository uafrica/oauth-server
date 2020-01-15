<?php

namespace OAuthServer\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\HttpException;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use OAuthServer\Bridge\ResourceServerFactory;
use OAuthServer\Traits\GetStorageTrait;

class OAuthAuthenticate extends BaseAuthenticate
{
    use GetStorageTrait;

    /**
     * @var ResourceServer
     */
    protected $Server;

    /**
     * Exception that was thrown by oauth server
     *
     * @var OAuthServerException
     */
    protected $_exception;

    /**
     * @var array
     */
    protected $_defaultConfig = [
        'continue' => false,
        'publicKey' => null,
        'fields' => [
            'username' => 'id',
        ],
        'userModel' => 'Users',
        'scope' => [],
        'finder' => 'all',
        'contain' => null,
    ];

    /**
     * @param ComponentRegistry $registry Component registry
     * @param array $config Config array
     */
    public function __construct(ComponentRegistry $registry, $config)
    {
        parent::__construct($registry, $config);

        if ($this->getConfig('server') && $this->getConfig('server') instanceof ResourceServer) {
            $this->Server = $this->getConfig('server');

            return;
        }
    }

    /**
     * @return ResourceServer
     */
    protected function getServer(): ResourceServer
    {
        if (!$this->Server) {
            $serverFactory = new ResourceServerFactory(
                $this->getConfig('publicKey', Configure::read('OAuthServer.publicKey'))
            );

            $this->Server = $serverFactory->create();
        }

        return $this->Server;
    }

    /**
     * Authenticate a user based on the request information.
     *
     * @param ServerRequest $request Request to get authentication information from.
     * @param Response $response A response object that can have headers added.
     * @return mixed
     */
    public function authenticate(ServerRequest $request, Response $response)
    {
        return $this->getUser($request);
    }

    /**
     * @param ServerRequest $request Request to get authentication information from.
     * @param Response $response A response object that can have headers added.
     * @return bool|Response
     */
    public function unauthenticated(ServerRequest $request, Response $response)
    {
        if ($this->getConfig('continue')) {
            return false;
        }

        if (isset($this->_exception)) {
            // ignoring $e->getHttpHeaders() for now
            // it only sends WWW-Authenticate header in case of InvalidClientException
            throw new HttpException($this->_exception->getMessage(), $this->_exception->getHttpStatusCode(), $this->_exception);
        }

        $message = __d('authenticate', 'You are not authenticated.');
        throw new BadRequestException($message);
    }

    /**
     * @param ServerRequest $request Request object
     * @return array|bool|mixed
     */
    public function getUser(ServerRequest $request)
    {
        try {
            $request = $this->getServer()->validateAuthenticatedRequest($request);
        } catch (OAuthServerException $e) {
            $this->_exception = $e;

            return false;
        }

        $userId = $request->getAttribute('oauth_user_id');

        $result = $this->_query($userId)->first();

        if (empty($result)) {
            return false;
        }

        return $result->toArray();
    }
}
