<?php

namespace OAuthServer\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Network\Exception\HttpException;
use Cake\Network\Response;
use League\OAuth2\Server\Exception\OAuthServerException;
use OAuthServer\Bridge\Entity\User;
use OAuthServer\Controller\Component\OAuthComponent;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * Class OAuthController
 *
 * @property OAuthComponent $OAuth
 * @mixin Controller
 */
class OAuthController extends AppController
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('OAuthServer.OAuth', (array)Configure::read('OAuthServer'));
        $this->loadComponent('RequestHandler');
    }

    /**
     * @param Event $event Event object.
     * @return void
     */
    public function beforeFilter(Event $event): void
    {
        parent::beforeFilter($event);

        if (!$this->components()->has('Auth')) {
            throw new RuntimeException('OAuthServer requires Auth component to be loaded and properly configured');
        }

        $this->Auth->allow(['oauth', 'accessToken']);
        $this->Auth->deny(['authorize']);
    }

    /**
     * @return void
     */
    public function oauth()
    {
        $this->redirect([
            'action' => 'authorize',
            '_ext' => $this->request->getParam('_ext'),
            '?' => $this->request->getQueryParams(),
        ], 301);
    }

    /**
     * @return Response|ResponseInterface|void
     */
    public function authorize()
    {
        try {
            $server = $this->OAuth->getServer();
            $authRequest = $server->validateAuthorizationRequest($this->request);

            $this->dispatchEvent('OAuthServer.beforeAuthorize', [$authRequest]);

            if ($userId = $this->Auth->user($this->OAuth->getPrimaryKey())) {
                $authRequest->setUser(new User($userId));
            }

            if ($this->request->getData('authorization') === 'Approve') {
                $authRequest->setAuthorizationApproved(true);
            }

            if ($this->request->is('post')) {
                $response = $server->completeAuthorizationRequest($authRequest, $this->response);

                $this->dispatchEvent('OAuthServer.afterAuthorize', [$authRequest]);

                return $response;
            }
        } catch (OAuthServerException $e) {
            if ($e->getErrorType() === 'access_denied') {
                $this->dispatchEvent('OAuthServer.afterDeny', [$authRequest]);

                $redirectUri = $authRequest->getRedirectUri() . http_build_query([
                        'error' => $e->getErrorType(),
                        'message' => $e->getMessage(),
                    ]);

                return $this->redirect($redirectUri);
            }

            // ignoring $e->getHttpHeaders() for now
            // it only sends WWW-Authenticate header in case of InvalidClientException
            throw new HttpException($e->getMessage(), $e->getHttpStatusCode(), $e);
        }

        $authParams = [
            'redirectUri' => $authRequest->getRedirectUri(),
            'client' => $authRequest->getClient(),
            'scopes' => $authRequest->getScopes(),
        ];
        $user = $this->Auth->user();

        $this->set(compact('authParams', 'user'));
        $this->set('__serialize', ['authParams', 'user']);
    }

    /**
     * @return Response|ResponseInterface|null
     */
    public function accessToken()
    {
        try {
            return $this->OAuth->getServer()->respondToAccessTokenRequest($this->request, $this->response);
        } catch (OAuthServerException $e) {
            // ignoring $e->getHttpHeaders() for now
            // it only sends WWW-Authenticate header in case of InvalidClientException
            throw new HttpException($e->getMessage(), $e->getHttpStatusCode(), $e);
        }
    }
}
