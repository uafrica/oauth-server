<?php

use Cake\Core\Configure;

/**
 * OAuthServer plugin creates controller that extends App\Controller\AppController class.
 * Config OAuthServer.appController allows to override the base controller class.
 */
$appControllerReal = Configure::read('OAuthServer.appController', 'App\Controller\AppController');
$appControllerAlias = 'OAuthServer\Controller\AppController';
class_alias($appControllerReal, $appControllerAlias);
// backward compatible <3.6.0
if (!class_exists('Cake\Http\Exception\HttpException')) {
    class_alias('Cake\Network\Exception\HttpException', 'Cake\Http\Exception\HttpException');
}
if (!class_exists('Cake\Http\Exception\BadRequestException')) {
    class_alias('Cake\Network\Exception\BadRequestException', 'Cake\Http\Exception\BadRequestException');
}
if (!class_exists('Cake\Http\Exception\NotImplementedException')) {
    class_alias('Cake\Network\Exception\NotImplementedException', 'Cake\Http\Exception\NotImplementedException');
}
if (!class_exists('Cake\Http\Exception\UnauthorizedException')) {
    class_alias('Cake\Network\Exception\UnauthorizedException', 'Cake\Http\Exception\UnauthorizedException');
}
