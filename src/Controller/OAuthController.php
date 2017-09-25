<?php
namespace OAuthServer\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\I18n\Time;
use Cake\Network\Exception\HttpException;
use Cake\Network\Response;
use Cake\ORM\Query;
use League\OAuth2\Server\Exception\AccessDeniedException;
use League\OAuth2\Server\Exception\OAuthException;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Util\RedirectUri;
use App\Controller\AppController;
use OAuthServer\Model\Entities\UserEntity;

/**
 * Class OAuthController
 *
 * @property \OAuthServer\Controller\Component\OAuthComponent $OAuth
 */
class OAuthController extends AppController
{
    /** @var AuthCodeGrant|null */
    private $authCodeGrant;

    /** @var array|null */
    private $authParams;

    /**
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('OAuthServer.OAuth', (array)Configure::read('OAuthServer'));
        $this->loadComponent('RequestHandler');
    }

    /**
     * @param \Cake\Event\Event $event Event object.
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        if (!$this->components()->has('Auth')) {
            throw new \RuntimeException("OAuthServer requires Auth component to be loaded and properly configured");
        }
        $this->eventManager()->off($this->Csrf);
        $this->Security->config('unlockedActions', 'accessToken');
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
            '_ext' => $this->request->param('_ext'),
            '?' => $this->request->query
        ], 301);
    }

    /**
     * @throws \League\OAuth2\Server\Exception\InvalidGrantException
     * @return Response|null
     */
    public function authorize()
    {
        $authRequest = $this->OAuth->Server->validateAuthorizationRequest($this->request);
        $clientId = $this->request->query('client_id');
        $ownerModel = $this->Auth->config('authenticate.all.userModel');
        $ownerId = $this->Auth->user(Configure::read("OAuthServer.models.{$ownerModel}.id") ?: 'id');

        $event = new Event('OAuthServer.beforeAuthorize', $this);
        EventManager::instance()->dispatch($event);

        $serializeKeys = [];
        if (is_array($event->result)) {
            $this->set($event->result);
            $serializeKeys = array_keys($event->result);

            if (isset($event->result['ownerId'])) {
                $ownerId = $event->result['ownerId'];
            }
            if (isset($event->result['ownerModel'])) {
                $ownerModel = $event->result['ownerModel'];
            }
        }

        if (($this->request->is('post') && $this->request->data('authorization') === 'Proceed to questionnaire')) {
            $user = new UserEntity();
            $user->setUserID($ownerId);
            $authRequest->setUser($user);
            $authRequest->setAuthorizationApproved(true);

            $event = new Event('OAuthServer.afterAuthorize', $this);
            EventManager::instance()->dispatch($event);

            return $this->OAuth->Server->completeAuthorizationRequest($authRequest, $this->response);
        } elseif ($this->request->is('post')) {
            $event = new Event('OAuthServer.afterDeny', $this);
            EventManager::instance()->dispatch($event);

            $error = new AccessDeniedException();

            $redirectUri = RedirectUri::make($this->authParams['redirect_uri'], [
                'error' => $error->errorType,
                'message' => $error->getMessage()
            ]);

            return $this->redirect($redirectUri);
        }

        $this->set('authParams', $this->authParams);
        $this->set('user', $this->Auth->user());
        $this->set('_serialize', array_merge(['user', 'authParams'], $serializeKeys));

        return null;
    }

    /**
     * @return void
     */
    public function accessToken()
    {
        $redirectUri = $this->request->getData('redirect_uri');
        $this->request->data['redirect_uri'] = urldecode($redirectUri);
        try {
            $response = $this->OAuth->Server->respondToAccessTokenRequest($this->request, $this->response);
            return $response;
        } catch (OAuthException $e) {
            // ignoring $e->getHttpHeaders() for now
            // it only sends WWW-Authenticate header in case of InvalidClientException
            throw new HttpException($e->getMessage(), $e->httpStatusCode, $e);
        }
    }
}
