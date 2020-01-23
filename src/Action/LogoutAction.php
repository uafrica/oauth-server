<?php

namespace OAuthServer\Action;

use Crud\Action\BaseAction;
use Crud\Event\Subject;
use Crud\Traits\RedirectTrait;

class LogoutAction extends BaseAction
{
    use RedirectTrait;

    protected $_defaultConfig = [
        'enabled' => true,
        'messages' => [
            'success' => [
                'text' => 'Successfully logged you out',
            ],
        ],
    ];

    /**
     * HTTP GET handler
     *
     * @return void|\Cake\Http\Response
     */
    protected function _get()
    {
        $subject = $this->_subject();
        $this->_trigger('beforeLogout', $subject);

        $subject->set([
            'success' => true,
            'redirectUrl' => $this->_controller()->Auth->logout(),
        ]);

        $this->_trigger('afterLogout', $subject);
        $this->setFlash('success', $subject);

        return $this->_redirect(
            $subject,
            $subject->redirectUrl
        );
    }
}
