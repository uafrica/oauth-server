<?php

use Cake\Core\Configure;

/**
 * OAuthServer plugin creates controller that extends Cake\Controller\Controller class.
 * Config OAuthServer.appController allows to override the base controller class
 */
$appControllerAlias = 'OAuthServer\Controller\AppController';
if (!class_exists($appControllerAlias)) {
    $appControllerReal = Configure::read('OAuthServer.appController') ?: 'Cake\Controller\Controller'; // not making assumption about existence of AppController
    class_alias($appControllerReal, $appControllerAlias);
}

