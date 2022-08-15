<?php

namespace OAuthServer\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use OAuthServer\Controller\Component\OAuthComponent;
use OAuthServer\Exception\ServiceNotAvailableException;
use OAuthServer\Lib\Enum\IndexMode;
use OAuthServer\Plugin;
use UnexpectedValueException;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Exception as PhpException;
use RuntimeException;

/**
 * OAuth 2.0 process controller
 *
 * Uses AppController alias in the current namespace
 * from bootstrap and config OAuthServer.appController
 *
 * @property OAuthComponent $OAuth
 */
class OAuthController extends AppController
{
    /**
     * OAuth 2.0 vendor authorization server object
     *
     * @var AuthorizationServer|null
     */
    protected ?AuthorizationServer $authorizationServer = null;

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('OAuthServer.OAuth');
        $this->authorizationServer = Plugin::instance()->getAuthorizationServer();
    }

    /**
     * @inheritDoc
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        if (!$this->components()->has('Auth')) {
            throw new RuntimeException('OAuthServer requires Auth component to be loaded and properly configured');
        }
        $this->Auth->allow(['oauth', 'accessToken', 'status']);
        $this->Auth->deny(['authorize']);
    }

    /**
     * Index action handler
     *
     * @return Response
     * @throws UnexpectedValueException
     * @throws NotFoundException
     */
    public function index(): Response
    {
        if (Configure::read('OAuthServer.serviceDisabled')) {
            throw new ServiceNotAvailableException();
        }
        $mode = new IndexMode(Configure::read('OAuthServer.indexMode') ?? IndexMode::DISABLED);
        switch ($mode->getValue()) {
            case IndexMode::REDIRECT_TO_AUTHORIZE:
                return $this->redirect([
                    'action' => 'authorize',
                    '_ext'   => $this->request->param('_ext'),
                    '?'      => $this->request->query,
                ], 301);
            case IndexMode::REDIRECT_TO_STATUS:
                return $this->redirect(['action' => 'status']);
        }
        throw new NotFoundException();
    }

    /**
     * Authorize action handler
     *
     * @return Response
     * @TODO JSON seems to be the standard, but improve content type handling?
     * @TODO improve exception handling?
     */
    public function authorize(): Response
    {
        if (Configure::read('OAuthServer.serviceDisabled')) {
            throw new ServiceNotAvailableException();
        }

        // Start authorization request
        $authRequest = $this->authorizationServer->validateAuthorizationRequest($this->request);
        $clientId    = $authRequest->getClient()->getIdentifier();

        // Once the user has logged in set the user on the AuthorizationRequest
        $user = $this->OAuth->getSessionUserData();
        $authRequest->setUser($user);

        $eventManager = EventManager::instance();
        $eventManager->dispatch(new Event('OAuthServer.beforeAuthorize', $this));

        try {
            // immediately approve authorization request if already has active tokens
            if ($this->OAuth->hasActiveAccessTokens($clientId, $user->getIdentifier())) {
                $authRequest->setAuthorizationApproved(true);
                $eventManager->dispatch(new Event('OAuthServer.afterAuthorize', $this));
                return $this->authorizationServer->completeAuthorizationRequest($authRequest, $this->response);
            }

            // handle form posted UI confirmation of client authorization approval
            if ($this->request->is('post')) {
                $authRequest->setAuthorizationApproved(false);
                if ($this->request->data('authorization') === 'Approve') {
                    $authRequest->setAuthorizationApproved(true);
                    $eventManager->dispatch(new Event('OAuthServer.afterAuthorize', $this));
                } else {
                    $eventManager->dispatch(new Event('OAuthServer.afterDeny', $this));
                }
                return $this->authorizationServer->completeAuthorizationRequest($authRequest, $this->response);
            }
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
        } catch (Exception $exception) {
            $body = new Stream('php://temp', 'r+');
            $body->write($exception->getMessage());
            return $response->withStatus(500)->withBody($body);
        }

        $this->OAuth->enrichScopes(...$authRequest->getScopes());
        $this->set('authRequest', $authRequest);
        return $this->response;
    }

    /**
     * Access token action handler
     *
     * @return Response
     * @TODO JSON seems to be the standard, but improve content type handling?
     * @TODO improve exception handling?
     */
    public function accessToken(): Response
    {
        if (Configure::read('OAuthServer.serviceDisabled')) {
            throw new ServiceNotAvailableException();
        }
        try {
            return $this->authorizationServer->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
        } catch (PhpException $exception) {
            return (new OAuthServerException($exception->getMessage(), 0, 'unknown_error', 500))
                ->generateHttpResponse($response);
        }
    }

    /**
     * Service status, documentation and operation parameters
     *
     * @return Response
     * @TODO JSON seems to be the standard, but improve content type handling?
     * @throws ServiceNotAvailableException
     */
    public function status(): Response
    {
        if (Configure::read('OAuthServer.statusDisabled')) {
            throw new ServiceNotAvailableException();
        }
        if (!$this->request->is('json')) {
            throw new NotFoundException();
        }
        $status = Plugin::instance()->getStatus();
        return $this->getResponse()
                    ->withType('json')
                    ->withStringBody(json_encode($status));
    }
}