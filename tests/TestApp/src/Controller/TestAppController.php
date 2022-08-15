<?php

namespace App\Controller;

use Cake\Controller\Component\AuthComponent;
use Cake\Controller\Controller;

/**
 * CLASS FOR TESTING PURPOSES
 */
class TestAppController extends Controller
{
    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $this->loadComponent('Auth', [
            'authenticate' => [
                AuthComponent::ALL => [
                    'userModel' => 'Users',
                ],
                'OAuthServer.OAuth',
                'Form',
            ],
            'loginAction'  => [
                'controller' => 'Users',
                'action'     => 'login',
            ],
        ]);
    }
}
