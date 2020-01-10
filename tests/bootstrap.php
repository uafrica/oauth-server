<?php
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
require_once 'vendor/cakephp/cakephp/src/basics.php';
require_once 'vendor/autoload.php';
define('ROOT', $root . DS . 'tests' . DS . 'test_app' . DS);
define('APP', ROOT);
define('CONFIG', $root . DS . 'config' . DS);
define('TMP', sys_get_temp_dir() . DS);

$loader = new \Cake\Core\ClassLoader();
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
Cake\Cache\Cache::config([
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

ConnectionManager::config('test', ['url' => getenv('db_dsn')]);

Configure::write('OAuthServer.appController', 'TestApp\Controller\TestAppController');

require_once $root . DS . 'config' . DS . 'bootstrap.php';

Plugin::load('OAuth', [
    'path' => dirname(dirname(__FILE__)) . DS,
]);
Plugin::load('OAuthServer', ['path' => $root]);

\Cake\Routing\DispatcherFactory::add('Routing');
\Cake\Routing\DispatcherFactory::add('ControllerFactory');
