<?php

namespace OAuthServer\Action;

use Crud\Action\BaseAction;
use Crud\Event\Subject;
use Crud\Traits\RedirectTrait;

class LoginAction extends BaseAction
{
    use RedirectTrait;

    protected $_defaultConfig = [
        'enabled' => true,
        'messages' => [
            'success' => [
                'text' => 'Successfully logged you in',
            ],
            'error' => [
                'text' => 'Invalid credentials, please try again',
            ],
        ],
    ];

    /**
     * HTTP GET handler
     *
     * @return void
     */
    protected function _get()
    {
        $subject = $this->_subject([
            'success' => true,
        ]);

        $this->_trigger('beforeRender', $subject);
    }

    /**
     * HTTP POST handler
     *
     * @return \Cake\Http\Response
     */
    protected function _post()
    {
        $subject = $this->_subject();

        $this->_trigger('beforeLogin', $subject);

        if ($user = $this->_controller()->Auth->identify()) {
            return $this->_success($subject, $user);
        }

        $this->_error($subject);
    }

    /**
     * Post success callback
     *
     * @param \Crud\Event\Subject $subject Event subject.
     * @param array $user Authenticated user record data.
     * @return \Cake\Http\Response
     */
    protected function _success(Subject $subject, array $user)
    {
        $subject->set(['success' => true, 'user' => $user]);

        $this->_trigger('afterLogin', $subject);
        $this->_controller()->Auth->setUser($subject->user);
        $this->setFlash('success', $subject);

        $redirectUri = $this->_controller()->Auth->redirectUrl();
        if ($this->_request()->query('redir') == "oauth") {
            $redirectUri = [
                'plugin' => 'OAuthServer',
                'controller' => 'OAuth',
                'action' => 'authorize',
                '?' => $this->_request()->query,
            ];
        }

        return $this->_redirect(
            $subject,
            $redirectUri
        );
    }

    /**
     * Post error callback
     *
     * @param \Crud\Event\Subject $subject Event subject
     * @return void
     */
    protected function _error(Subject $subject)
    {
        $subject->set(['success' => false]);

        $this->_trigger('afterLogin', $subject);
        $this->setFlash('error', $subject);
        $this->_trigger('beforeRender', $subject);
    }
}
