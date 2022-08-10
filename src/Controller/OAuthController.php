<?php

namespace OAuthServer\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use OAuthServer\Controller\Component\OAuthComponent;
use OAuthServer\Lib\Enum\IndexMode;
use OAuthServer\Plugin;
use UnexpectedValueException;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Exception as PhpException;

/**
 * OAuth 2.0 process controller
 *
 * @TODO specify which base controller to use?
 *
 * @property OAuthComponent $OAuth
 */
class OAuthController extends Controller
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
     * Index action handler
     *
     * @return Response
     * @throws UnexpectedValueException
     * @throws NotFoundException
     */
    public function index(): Response
    {
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
        // Start authorization request
        $authRequest = $this->authorizationServer->validateAuthorizationRequest($this->request);
        $clientId    = $authRequest->getClient()->getIdentifier();

        // Once the user has logged in set the user on the AuthorizationRequest
        $user = $this->OAuth->getSessionUserData();
        $authRequest->setUser($authRequest);

        $event = new Event('OAuthServer.beforeAuthorize', $this);
        EventManager::instance()->dispatch($event);

        try {
            // immediately approve authorization request if already has active tokens
            if ($this->OAuth->hasActiveAccessTokens($clientId, $user->getIdentifier())) {
                $authRequest->setAuthorizationApproved(true);
                return $this->authorizationServer->completeAuthorizationRequest($authRequest, $this->response);
            }

            // handle form posted UI confirmation of client authorization approval
            if ($this->request->is('post')) {
                $authRequest->setAuthorizationApproved(false);
                if ($this->request->data('authorization') === 'Approve') {
                    $authRequest->setAuthorizationApproved(true);
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
     * @return void
     */
    public function status()
    {
        // document (https://www.oauth.com/oauth2-servers/creating-documentation/)
        // - client Registration
        // - available endpoints
        // - service status
        // - supported grant types
        // - supported extensions / access token response
        // - supports refresh tokens and under which conditions / grant types,
        // - access token and refresh token TTL
    }
}