<?php

namespace OAuthServer\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\I18n\Time;
use Cake\ORM\Query;
use UnexpectedValueException;
use OAuthServer\Lib\Enum\IndexMode;
use Cake\Controller\Controller;

// @TODO specify which base controller to use?
use Exception as PhpException;
use League\OAuth2\Server\Exception\OAuthServerException;

/**
 * OAuth 2.0 process controller
 */
class OAuthController extends Controller
{
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
     */
    public function authorize(): Response
    {
        $authServer = new \League\OAuth2\Server\AuthorizationServer(); // @TODO get proper auth server

        $currentTokens = $this
            ->loadModel('OAuthServer.AccessTokens')
            ->find()
            ->where(['expires > ' => Time::now()->getTimestamp()])
            ->matching('Sessions', function (Query $q) use ($ownerModel, $ownerId, $clientId) {
                return $q->where([
                    'owner_model' => $ownerModel,
                    'owner_id'    => $ownerId,
                    'client_id'   => $clientId,
                ]);
            })
            ->count();

        if ($currentTokens > 0 || ($this->request->is('post') && $this->request->data('authorization') === 'Approve')) {
            $redirectUri = $this->authCodeGrant->newAuthorizeRequest($ownerModel, $ownerId, $this->authParams);
            EventManager::instance()->dispatch(new Event('OAuthServer.afterAuthorize', $this));
            return $this->redirect($redirectUri);
        } elseif ($this->request->is('post')) {

        }

        /*try {
             // Validate the HTTP request and return an AuthorizationRequest object.
             // The auth request object can be serialized into a user's session
             $authRequest = $authServer->validateAuthorizationRequest($request);

             // Once the user has logged in set the user on the AuthorizationRequest
             $authRequest->setUser(new UserEntity()); // @TODO implement UserEntityInterface

             // Once the user has approved or denied the client update the status
             // (true = approved, false = denied)
             $authRequest->setAuthorizationApproved(true);

             // Return the HTTP redirect response
             return $authServer->completeAuthorizationRequest($authRequest, $response);
         } catch (OAuthServerException $exception) {
             return $exception->generateHttpResponse($response);
         } catch (\Exception $exception) {
             $body = new Stream('php://temp', 'r+');
             $body->write($exception->getMessage());

             return $response->withStatus(500)->withBody($body);
         }*/

        try {
            $response = $this->server->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
            // @codeCoverageIgnoreStart
        } catch (Exception $exception) {
            return (new OAuthServerException($exception->getMessage(), 0, 'unknown_error', 500))
                ->generateHttpResponse($response);
            // @codeCoverageIgnoreEnd
        }

    }

    /**
     * Access token action handler
     *
     * @return Response
     * @TODO JSON seems to be the standard, but improve content type handling?
     */
    public function accessToken(): Response
    {


        $authServer = new \League\OAuth2\Server\AuthorizationServer(); // @TODO get proper auth server
        try {
            return $authServer->respondToAccessTokenRequest($request, $response);
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