<?php

use Cake\Core\Configure;

/**
 * OAuthServer plugin creates controller that extends App\Controller\AppController class.
 * Config OAuthServer.appController allows to override the base controller class.
 */
$appControllerReal = Configure::read('OAuthServer.appController', 'App\Controller\AppController');
$appControllerAlias = 'OAuthServer\Controller\AppController';
class_alias($appControllerReal, $appControllerAlias);
