<?php

// Setup constants
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
define('ROOT', dirname(__DIR__));
define('APP_DIR', 'src');
define('APP_ROOT', ROOT . DS . 'tests' . DS . 'TestApp' . DS);
define('APP', APP_ROOT . APP_DIR . DS);
define('CONFIG', APP_ROOT . DS . 'config' . DS);
define('WWW_ROOT', APP . DS . 'webroot' . DS);
define('TESTS', ROOT . DS . 'tests' . DS);
define('TMP', APP_ROOT . DS . 'tmp' . DS);
define('LOGS', APP_ROOT . DS . 'logs' . DS);
define('CACHE', TMP . 'cache' . DS);
define('CAKE_CORE_INCLUDE_PATH', ROOT . DS . 'vendor' . DS . 'cakephp' . DS . 'cakephp');
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
define('CAKE', CORE_PATH . 'src' . DS);

// Run vendor autoloader
require_once ROOT . DS . 'vendor' . DS . 'autoload.php';

// Bootstrap CakePHP core library
require_once CORE_PATH . 'config' . DS . 'bootstrap.php';

// Setup test application namespace for the integration tests app that goes in ROOT/tests/test_app
$loader = new \Cake\Core\ClassLoader();
$loader->register();
$loader->addNamespace('App', APP);

// Setup TestApp application configuration
$config = [
    'debug'   => true,
    'App'     => [
        'namespace'     => 'App',
        'encoding'      => 'UTF-8',
        'defaultLocale' => 'en_US',
        'base'          => false,
        'baseUrl'       => false,
        'dir'           => 'src',
        'webroot'       => 'webroot',
        'wwwRoot'       => WWW_ROOT,
        'fullBaseUrl'   => 'http://localhost',
        'imageBaseUrl'  => 'img/',
        'cssBaseUrl'    => 'css/',
        'jsBaseUrl'     => 'js/',
        'paths'         => [
            'plugins'   => [APP_ROOT . 'plugins' . DS],
            'templates' => [APP . 'Template' . DS],
            'locales'   => [APP . 'Locale' . DS],
        ],
    ],
    'plugins' => [
        'OAuthServer' => ROOT . DS,
        'Migrations'  => ROOT . 'vendor' . DS . 'cakephp' . DS . 'migrations' . DS,
    ],
    'Error'   => [
        'exceptionRenderer' => \App\Error\ExceptionRenderer::class,
    ],
];

\Cake\Core\Configure::write($config);

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

// Load plugin default configuration
\Cake\Core\Configure::write(include ROOT . DS . 'config' . DS . 'plugin.default.php');

// Set plugin OAuthController AppController alias to the controller from the test application
\Cake\Core\Configure::write('OAuthServer.appController', 'App\Controller\TestAppController');

// PHP settings
error_reporting(E_ALL & ~E_USER_DEPRECATED);
ini_set('intl.default_locale', \Cake\Core\Configure::read('App.defaultLocale'));
mb_internal_encoding(\Cake\Core\Configure::read('App.encoding'));
date_default_timezone_set('UTC');

// Setup sqlite test database configuration
if (!getenv('db_dsn')) {
    putenv('db_dsn=sqlite:///:memory:');
}
\Cake\Datasource\ConnectionManager::setConfig('test', ['url' => getenv('db_dsn')]);
\Cake\Datasource\ConnectionManager::setConfig('test_migrations', ['url' => 'sqlite:///:memory:']);

// Load test application plugins (including self load)
\Cake\Core\Plugin::load('Migrations');
\Cake\Core\Plugin::load('OAuthServer', ['bootstrap' => true, 'routes' => true]);
\Cake\Core\Plugin::routes('OAuthServer');