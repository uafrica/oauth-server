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
require_once $root . '/vendor/cakephp/cakephp/src/basics.php';
require_once $root . '/vendor/autoload.php';
define('ROOT', $root . DS . 'tests' . DS . 'test_app' . DS);
define('APP', ROOT);
define('CONFIG', $root . DS . 'config' . DS);
define('TMP', sys_get_temp_dir() . DS);

$loader = new ClassLoader();
$loader->register();
$loader->addNamespace('TestApp', APP);

Configure::write('debug', true);
Configure::write('App', [
    'namespace' => 'App',
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

Configure::write('OAuthServer.appController', 'TestApp\Controller\TestAppController');

// Disable deprecations for now when using 3.6
if (version_compare(Configure::version(), '3.6.0', '>=')) {
    error_reporting(E_ALL ^ E_USER_DEPRECATED);
}

Plugin::load('OAuthServer', ['path' => $root, 'bootstrap' => true, 'route' => true]);

error_reporting(E_ALL);
