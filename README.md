# OAuth 2.0 Server for CakePHP 3
Documentation README.md for `uafrica/oauth-server`

## Features
### Basic OAuth 2.0 implementation
Implementation of package `league/oauth2-server`
### Extension: OpenID Connect
Implementation of package `steverhoades/oauth2-openid-connect-server`
### Optional status endpoint
Visit using `oauth/status`
### Unit and integration tests
Run using `compsoser install` and `composer test`
## Installation
### Composer
Installation is done using composer. Run:
```bash
$ composer require uafrica/oauth-server
```
Once composer has installed the package, the plugin needs to be activated by running:
```bash
$ bin/cake plugin load -r OAuthServer
```
Or by loading the plugin in your application's `config/bootstrap.php` file or `Application::addPlugin()` call.
### Load your own configuration file based on the default config
Use this plugin's `config/plugin.default.php` file to configure your OAuth 2.0 service by loading a configuration
tailored to your application's requirements in a custom configuration file under your application's `Config` directory.

Load using `Configure::load('yourconfigname')` in your application's `bootstrap.php` file. See default configuration for more information.
### Manually create SSL key files
1. Create a private key
   ```bash
   $ openssl genrsa -out private.key 2048
   ```
2. Create a public key
   ```bash
   $ openssl rsa -in private.key -pubout > public.key
   ```
3. Change permissions of the .key files (or a PHP Notice will be thrown)
   ```bash
   $ chmod 660 *.key
   ```
4. Update paths to point to your created keys in your custom configuration file if required. Optionally provide the private key's password if it was generated with a password.
   ```php
   'privateKey'           => [
       'path'     => 'file://' . __DIR__ . '/private.key',
       'password' => null,
   ],
   'publicKey'            => [
       'path' => 'file://' . __DIR__ . '/public.key',
   ],
   ```
### Setting encryption key
1. Run the following command from the application implementing this plugin:
   ```bash
   $ bin/cake oauth generate_encryption_key >> encryption.key
   ```
2. Update your `encryptionKey` config key with the value from the contents of the encryption.key file.
### Migrations
Finally the database migrations need to be run:
```bash
$ bin/cake migrations migrate --plugin OAuthServer
```

## Implementation
### Custom repository implementations
Repository implementations are provided by default by the plugin. Except for the user repository.

The user repository requires an implementation of `League\OAuth2\Server\Repositories\UserRepositoryInterface` configured
in the repository mapping configuration key `OAuthServer.repositories` using:
```
\OAuthServer\Lib\Enum\Repository::USER => 'CustomTableAliasOfUsers'
```
### Authorizing users using the `authorization_code` grant
Change your login method to look as follows:

```php
public function login()
{
    if ($this->request->is('post')) {
        $user = $this->Auth->identify();
        if ($user) {
            $this->Auth->setUser($user);
            $redirectUri = $this->Auth->redirectUrl();
            if ($this->request->query['redir'] === 'oauth') {
                $redirectUri = [
                    'plugin' => 'OAuthServer',
                    'controller' => 'OAuth',
                    'action' => 'authorize',
                    '?' => $this->request->query
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

### Protecting your resources using an access_token
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
### Clients
Your application will need to provide a way to add/edit/delete clients.
### Scopes
Your application will need to provide a way to add/edit/delete scopes.
### Other useful points
- Access tokens table depend on an 'active' finder that will return all active access tokens for the given 'client_id' and 'user_id'. Useful to know in case you're replacing the access tokens repository using a custom mapping in the configuration.
- Check throws of OAuthServerExceptions to map to proper HTTP status codes when calling bare oauth endpoints.