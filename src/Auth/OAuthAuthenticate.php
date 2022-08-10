<?php

namespace OAuthServer\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Log\Log;
use Cake\Network\Request;
use Cake\Network\Response;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use OAuthServer\Exception\Exception;
use Cake\Event\Event;
use Cake\Event\EventManager;
use OAuthServer\Plugin;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Controller\ComponentRegistry;

/**
 * The CakePHP OAuth 2.0 Authenticate object
 *
 * This 'Authenticate' object is the protection layer
 * of the accessible resources (resource server section of the application)
 * that are made available to the access token
 */
class OAuthAuthenticate extends BaseAuthenticate
{
    /**
     * OAuth 2.0 resource server object
     *
     * @var ResourceServer
     */
    protected ResourceServer $_resourceServer;

    /**
     * Exception that was thrown by OAuth 2.0 server
     *
     * @var OAuthServerException|null
     */
    protected ?OAuthServerException $_exception;

    /**
     * Attributes the resource server adds upon authenticating the request
     * that identify the request to a user or client
     *
     * @var string[]
     */
    protected array $userIdentifiableAttributes = [
        'oauth_access_token_id',
        'oauth_client_id',
        'oauth_user_id',
        'oauth_scopes',
    ];

    /**
     * @inheritDoc
     */
    public function __construct(ComponentRegistry $registry, array $config)
    {
        parent::__construct($registry, $config);
        $this->_resourceServer = Plugin::instance()->getResourceServer();
    }

    /**
     * @inheritDoc
     */
    public function authenticate(Request $request, Response $response)
    {
        return $this->getUser($request);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getUser(Request $request)
    {
        if (!$request = $this->getValidatedRequestWithAuthAttributes($request)) {
            return false;
        }
        $user = $this->getUserIdentifiableAttributesFromRequest($request);
        if (!$this->dispatchGetUserEvent($request, $user)) {
            return false;
        }
        return $user;
    }

    /**
     * Validate and add authentication attributes to the given request.
     * Will set exception to $this->_exception if thrown from validation of request
     *
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface|null Will return modified request or null if failed to validate
     */
    public function getValidatedRequestWithAuthAttributes(ServerRequestInterface $request): ?ServerRequestInterface
    {
        try {
            // modified request
            $request = $this->_resourceServer->validateAuthenticatedRequest($request);
        } catch (OAuthServerException $e) {
            Log::error($e);
            $this->_exception = $e;
            return null;
        }
        return $request;
    }

    /**
     * Extract request attributes to return as an identified OAuth 2.0 user
     *
     * @param ServerRequestInterface $request
     * @return array e.g. ['oauth_client_id' => '123', ...]
     * @throws Exception
     */
    public function getUserIdentifiableAttributesFromRequest(ServerRequestInterface $request): array
    {
        $user = array_intersect_key($request->getAttributes(), array_flip($this->userIdentifiableAttributes));
        if (empty($user)) {
            throw new Exception('Resource server is always expected to fulfill user attributes at this point');
        }
        return $user;
    }

    /**
     * Throw event for any required user data hooks/mutations
     *
     * @param ServerRequestInterface  $request
     * @param array                  &$user
     * @return bool False if stopped
     */
    public function dispatchGetUserEvent(ServerRequestInterface $request, array &$user): bool
    {
        $event = new Event('OAuthServer.getUser', $request, $user);
        EventManager::instance()->dispatch($event);
        if (is_array($event->result)) {
            $user = $event->result;
        }
        if ($event->isStopped()) {
            $msg = 'event %s was stopped for user %s';
            Log::warning(sprintf($msg, $event->getName(), json_encode($user)));
            return false;
        }
        return true;
    }

    /**
     * Return exception that was thrown by OAuth 2.0 server
     *
     * @return OAuthServerException|null
     */
    public function getException(): ?OAuthServerException
    {
        return $this->_exception;
    }
}