<?php

require_once 'vendor/cakephp/cakephp/src/basics.php';
require_once 'vendor/autoload.php';

// determine plugin folder root
$findRoot = function ($root) {
    do {
        $lastRoot = $root;
        $root     = dirname($root);
        if (is_dir($root . '/vendor/cakephp/cakephp')) {
            return $root;
        }
    } while ($root !== $lastRoot);
    throw new Exception('Cannot find the root of the application, unable to run tests');
};
$root     = $findRoot(__FILE__);
unset($findRoot);
chdir($root);

error_reporting(E_ALL & ~E_USER_DEPRECATED);

// set cake application constants
define('ROOT', $root . DS . 'tests' . DS . 'test_app' . DS);
define('APP', ROOT);
define('CONFIG', $root . DS . 'config' . DS);
define('TMP', sys_get_temp_dir() . DS);

// setup test application namespace for the integration tests app that goes in ROOT/tests/test_app
$loader = new \Cake\Core\ClassLoader();
$loader->register();
$loader->addNamespace('TestApp', APP);

// setup application configuration
Cake\Core\Configure::write('debug', true);
Cake\Core\Configure::write('App', [
    'namespace' => 'App',
    'paths'     => [
        'plugins'   => [ROOT . 'Plugin' . DS],
        'templates' => [ROOT . 'Template' . DS],
    ],
]);

// setup test application cache
Cake\Cache\Cache::setConfig([
    '_cake_core_'  => [
        'engine'    => 'File',
        'prefix'    => 'cake_core_',
        'serialize' => true,
        'path'      => '/tmp',
    ],
    '_cake_model_' => [
        'engine'    => 'File',
        'prefix'    => 'cake_model_',
        'serialize' => true,
        'path'      => '/tmp',
    ],
]);

// setup sqlite test database configuration
if (!getenv('db_dsn')) {
    putenv('db_dsn=sqlite:///:memory:');
}
\Cake\Datasource\ConnectionManager::setConfig('test', ['url' => getenv('db_dsn')]);

// plugin specific configurations
\Cake\Core\Configure::write(include CONFIG . 'plugin.default.php');
// @TODO implement this yet in controller?
\Cake\Core\Configure::write('OAuthServer.appController', 'TestApp\Controller\TestAppController');

// bootstrap plugin
require_once CONFIG . 'bootstrap.php';

\Cake\Core\Plugin::load('OAuth', ['path' => dirname(dirname(__FILE__)) . DS]);
// @TODO delete line above?

\Cake\Core\Plugin::load('OAuthServer', ['path' => $root]);

\Cake\Routing\DispatcherFactory::add(\Cake\Routing\Filter\RoutingFilter::class);
\Cake\Routing\DispatcherFactory::add(\Cake\Routing\Filter\ControllerFactoryFilter::class);