<?php

use Cake\Core\ClassLoader;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Routing\DispatcherFactory;

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

require_once 'vendor/cakephp/cakephp/src/basics.php';
require_once 'vendor/autoload.php';
define('ROOT', $root . DS . 'tests' . DS . 'test_app' . DS);
define('APP', ROOT);
define('CONFIG', $root . DS . 'config' . DS);
define('TMP', sys_get_temp_dir() . DS);
define('CAKE_CORE_INCLUDE_PATH', $root . '/vendor/cakephp/cakephp');
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);

$loader = new ClassLoader();
$loader->register();
$loader->addNamespace('TestApp', APP);
Configure::write('debug', true);
Configure::write('App', [
    'namespace' => 'TestApp',
    'base' => '',
    'paths' => [
        'plugins' => [ROOT . 'Plugin' . DS],
        'templates' => [ROOT . 'Template' . DS],
    ],
]);
Cake\Cache\Cache::setConfig([
    '_cake_core_' => [
        'engine' => 'File',
        'prefix' => 'cake_core_',
        'serialize' => true,
        'path' => '/tmp',
    ],
    '_cake_model_' => [
        'engine' => 'File',
        'prefix' => 'cake_model_',
        'serialize' => true,
        'path' => '/tmp',
    ],
]);
if (!getenv('db_dsn')) {
    putenv('db_dsn=sqlite:///:memory:');
}
if (!getenv('DB')) {
    putenv('DB=sqlite');
}
ConnectionManager::setConfig('test', ['url' => getenv('db_dsn')]);

Configure::write('OAuthServer', [
    'appController' => 'TestApp\Controller\TestAppController',
    'privateKey' => $root . '/tests/Fixture/test.pem',
    'publicKey' => $root . '/tests/Fixture/test-pub.pem',
    'encryptionKey' => 'def00000dc02308bae1781f846e667ca557628277485ba7c5ce897b74deb7b26ba1429a5b08d775708626a7f0664688d46f102066aefbf47e0000798043d11e06574ea83',
    'deleteAuthCodeOnRevoke' => false,
    'deleteAccessTokenOnRevoke' => false,
    'deleteRefreshTokenOnRevoke' => false,
]);

// Disable deprecations for now when using 3.6
if (version_compare(Configure::version(), '3.6.0', '>=')) {
    error_reporting(E_ALL ^ E_USER_DEPRECATED);
}

Plugin::load('OAuthServer', ['path' => $root . DS, 'bootstrap' => true, 'route' => true]);

error_reporting(E_ALL);
