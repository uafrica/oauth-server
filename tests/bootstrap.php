<?php

use Cake\Core\ClassLoader;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;

$findRoot = function ($root) {
    do {
        $lastRoot = $root;
        $root = dirname($root);
        if (is_dir($root . '/vendor/cakephp/cakephp')) {
            return $root;
        }
    } while ($root !== $lastRoot);
    throw new Exception('Cannot find the root of the application, unable to run tests');
};
$root = $findRoot(__FILE__);
unset($findRoot);
chdir($root);

require $root . '/vendor/cakephp/cakephp/tests/bootstrap.php';

Configure::write('OAuthServer', [
    'appController' => 'TestApp\Controller\TestAppController',
    'privateKey' => $root . '/tests/Fixture/test.pem',
    'publicKey' => $root . '/tests/Fixture/test-pub.pem',
    'encryptionKey' => 'def00000dc02308bae1781f846e667ca557628277485ba7c5ce897b74deb7b26ba1429a5b08d775708626a7f0664688d46f102066aefbf47e0000798043d11e06574ea83',
]);

// Disable deprecations for now when using 3.6
if (version_compare(Configure::version(), '3.6.0', '>=')) {
    error_reporting(E_ALL ^ E_USER_DEPRECATED);
}

Plugin::load('OAuthServer', ['path' => $root . DS, 'bootstrap' => true, 'route' => true]);

error_reporting(E_ALL);
