# OAuth2 Server for CakePHP 3

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)
[![Build Status](https://img.shields.io/travis/nojimage/cakephp-oauth-server/0.8.x.svg?style=flat-square)](https://travis-ci.org/nojimage/cakephp-oauth-server)

A plugin for implementing an OAuth2 server in CakePHP 3. Built on top of the [PHP League's OAuth2 Server](http://oauth2.thephpleague.com/). Currently we support the following grant types: AuthCode, RefreshToken, ClientCredentials.

This repository is a fork of [uafrica/oauth-server](https://github.com/uafrica/oauth-server).

## Installation

You can install this plugin into your CakePHP application using. Run:

```bash
composer require elstc/cakephp-oauth-server
```

Once composer has installed the package, the plugin needs to be activated by running:

```bash
bin/cake plugin load -b -r OAuthServer
```

The database migrations need to be run.

```bash
bin/cake migrations migrate -p OAuthServer
```

Generating `private and public keys` (see also https://oauth2.thephpleague.com/installation/):

```bash
openssl genrsa -out config/oauth.pem 2048
openssl rsa -in config/oauth.pem -pubout -out config/oauth.pub
```

Generating `encryption key` :

```bash
vendor/bin/generate-defuse-key
(COPY result hash)
```

Change your app.php, Add `OAuthServer` configuration :

```bash
    'OAuthServer' => [
        'privateKey' => CONFIG . 'oauth.pem',
        'publicKey' => CONFIG . 'oauth.pub',
        'encryptionKey' => 'def0000060c80a6856e8...', // <- SET encryption key FROM `vendor/bin/generate-defuse-key`
    ],
```

NOTICE: private key and encryption key is confidential. Try to set as much as possible with environment variables and not upload to the source code repository.

## Configuration

It is assumed that you already have working Form based authentication using the built in CakePHP 3 authentication component.
If you do not, please read [the authentication chapter](http://book.cakephp.org/3.0/en/controllers/components/authentication.html).

Set OAuthServer as an authentication adaptor.

In your `AppController::beforeFilter()` method, add (or modify)

```php
$this->Auth->config('authenticate', [
    'Form',
    'OAuthServer.OAuth'
]);
```

Change your login method to look as follows:

```php
public function login()
{
    if ($this->request->is('post')) {
        $user = $this->Auth->identify();
        if ($user) {
            $this->Auth->setUser($user);

            $redirectUri = $this->Auth->redirectUrl();
            if ($this->request->getQuery('redir') === 'oauth') {
                $redirectUri = [
                    'plugin' => 'OAuthServer',
                    'controller' => 'OAuth',
                    'action' => 'authorize',
                    '?' => $this->request->getQueryParams(),
                ];
            }

            return $this->redirect($redirectUri);
        } else {
            $this->Flash->error(
                __('Username or password is incorrect'),
                'default',
                [],
                'auth'
            );
        }
    }
}
```

Alternatively, if you are using the [Friends Of Cake CRUD plugin](https://github.com/friendsofcake/crud), add

```php
'login' => [
    'className' => 'OAuthServer.Login'
]
```

to your CRUD actions config.

## Usage

The base OAuth2 path is `example.com/oauth`.

In order to add clients and OAuth scopes you need to create a `ClientsController` and a `ScopesController` (Which is not part of this plugin)

The simplest way is to make use of the [Friends Of Cake CRUD-View plugin](https://github.com/friendsofcake/crud-view).

Install it by running

```bash
$ composer require friendsofcake/bootstrap-ui:dev-master
$ composer require friendsofcake/crud:dev-master
$ composer require friendsofcake/crud-view:dev-master
```

Then create a `ClientsController` that looks like:

```php
<?php
namespace App\Controller;

use Crud\Controller\ControllerTrait;

/**
 * OauthClients Controller
 *
 * @property \OAuthServer\Model\Table\OauthClientsTable $Clients
 */
class ClientsController extends AppController
{

    use ControllerTrait;

    public $modelClass = 'OAuthServer.Clients';

    /**
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->viewClass = 'CrudView\View\CrudView';
        $tables = [
            'Clients',
            'Scopes'
        ];
        $this->loadComponent('Crud.Crud', [
            'actions' => [
                'index' => [
                    'className' => 'Crud.Index',
                    'scaffold' => [
                        'tables' => $tables
                    ]
                ],
                'view' => [
                    'className' => 'Crud.View',
                    'scaffold' => [
                        'tables' => $tables
                    ]
                ],
                'edit' => [
                    'className' => 'Crud.Edit',
                    'scaffold' => [
                        'tables' => $tables,
                        'fields' => [
                            'name',
                            'redirect_uri',
                            'parent_model',
                            'parent_id' => [
                                'label' => 'Parent ID',
                                'type' => 'text'
                            ]
                        ]
                    ]
                ],
                'add' => [
                    'className' => 'Crud.Add',
                    'scaffold' => [
                        'tables' => $tables,
                        'fields' => [
                            'name',
                            'redirect_uri',
                            'parent_model',
                            'parent_id' => [
                                'label' => 'Parent ID',
                                'type' => 'text'
                            ]
                        ]
                    ]
                ],
                'delete' => [
                    'className' => 'Crud.Delete',
                    'scaffold' => [
                        'tables' => $tables
                    ]
                ],
            ],
            'listeners' => [
                'CrudView.View',
                'Crud.RelatedModels',
                'Crud.Redirect',
                'Crud.Api'
            ],
        ]);
    }
}
```

And a `ScopesController` that looks like:

```php
<?php
namespace App\Controller;

use Crud\Controller\ControllerTrait;

/**
 * Scopes Controller
 *
 * @property \OAuthServer\Model\Table\OauthScopesTable $Scopes
 */
class ScopesController extends AppController
{

    use ControllerTrait;

    public $modelClass = 'OAuthServer.Scopes';

    /**
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->viewClass = 'CrudView\View\CrudView';
        $tables = [
            'Clients',
            'Scopes'
        ];
        $this->loadComponent('Crud.Crud', [
            'actions' => [
                'index' => [
                    'className' => 'Crud.Index',
                    'scaffold' => [
                        'tables' => $tables
                    ]
                ],
                'view' => [
                    'className' => 'Crud.View',
                    'scaffold' => [
                        'tables' => $tables
                    ]
                ],
                'edit' => [
                    'className' => 'Crud.Edit',
                    'scaffold' => [
                        'tables' => $tables,
                        'fields' => [
                            'id' => [
                                'label' => 'ID',
                                'type' => 'text'
                            ],
                            'description',
                        ]
                    ]
                ],
                'add' => [
                    'className' => 'Crud.Add',
                    'scaffold' => [
                        'tables' => $tables,
                        'fields' => [
                            'id' => [
                                'label' => 'ID',
                                'type' => 'text'
                            ],
                            'description',
                        ]
                    ]
                ],
                'delete' => [
                    'className' => 'Crud.Delete',
                    'scaffold' => [
                        'tables' => $tables
                    ]
                ],
            ],
            'listeners' => [
                'CrudView.View',
                'Crud.RelatedModels',
                'Crud.Redirect',
            ],
        ]);
    }
}
```

## Customisation

The OAuth2 Server can be customised, the look for the various pages can be changed by creating templates in `Template/Plugin/OAuthServer/OAuth`

The server also fires a number of events that can be used to inject values into the process. The current events fired are:

* `OAuthServer.beforeAuthorize` - On rendering of the approval page for the user.
* `OAuthServer.afterAuthorize` - On the user authorising the client
* `OAuthServer.afterDeny` - On the user denying the client

You can customise the OAuth authorise page by creating a overriding template file in `src/Template/Plugin/OAuthServer/OAuth/authorize.ctp`
