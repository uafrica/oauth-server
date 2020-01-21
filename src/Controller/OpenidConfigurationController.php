<?php

namespace OAuthServer\Controller;

use ArrayObject;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use OAuthServer\Bridge\GrantTypes;
use OAuthServer\Controller\Component\OAuthComponent;

/**
 * Class OpenidConfigurationController
 *
 * @property OAuthComponent $OAuth
 */
class OpenidConfigurationController extends Controller
{
    /**
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('OAuthServer.OAuth', Configure::read('OAuthServer', []));

        $this->viewBuilder()->setClassName('Json');
    }

    /**
     * /.well-known/openid-configuration
     *
     * @return void
     * @link https://openid.net/specs/openid-connect-discovery-1_0.html#ProviderMetadata
     * @link https://openid.net/specs/openid-connect-discovery-1_0.html#ProviderConfigurationResponse
     */
    public function view()
    {
        $data = new ArrayObject([
            'issuer' => rtrim(Router::url('/', true), '/'),
            'authorization_endpoint' => Router::url([
                'plugin' => 'OAuthServer',
                'controller' => 'OAuth',
                'action' => 'authorize',
            ], true),
            'token_endpoint' => Router::url([
                'plugin' => 'OAuthServer',
                'controller' => 'OAuth',
                'action' => 'accessToken',
            ], true),
            'jwks_uri' => Router::url(['action' => 'jwks', '_ext' => 'json'], true),
            'response_types_supported' => ['code'],
            'subject_types_supported' => ['public'],
            'id_token_signing_alg_values_supported' => ['RS256'],
            'token_endpoint_auth_methods_supported' => array_map(static function ($method) {
                return GrantTypes::convertFromGrantClassName($method . 'Grant');
            }, $this->OAuth->getConfig('supportedGrants')),
        ]);

        $event = $this->dispatchEvent('OAuthServer.buildOpenidConfiguration', [$data]);

        if (!$event->isStopped() && $event->getResult()) {
            $data = $event->getResult();
        }

        $data = (array)$data;

        $this->set($data);
        $this->set('_serialize', array_keys($data));
    }

    /**
     * /oauth/jwks.json
     *
     * @return void
     * @link https://tools.ietf.org/html/rfc7517
     */
    public function jwks()
    {
        $publicKeyPath = $this->OAuth->getConfig('publicKey');

        $kid = hash_file('sha256', $publicKeyPath);
        $use = 'sig';
        $kty = 'RSA';
        $alg = 'RS256';

        $pubkeyDetails = openssl_pkey_get_details(openssl_pkey_get_public(file_get_contents($publicKeyPath)));

        $n = static::urlsafeB64Encode(Hash::get($pubkeyDetails, 'rsa.n'));
        $e = static::urlsafeB64Encode(Hash::get($pubkeyDetails, 'rsa.e'));

        $keys = new ArrayObject([]);
        $keys[] = compact('kid', 'e', 'kty', 'alg', 'n', 'use');

        $event = $this->dispatchEvent('OAuthServer.buildJWKs', [$keys]);

        if (!$event->isStopped() && $event->getResult()) {
            $keys = $event->getResult();
        }

        $keys = (array)$keys;

        $this->set('keys', $keys);
        $this->set('_serialize', ['keys']);
    }

    /**
     * @param string $input raw string
     * @return string
     * @see \Firebase\JWT\JWT::urlsafeB64Encode()
     */
    private static function urlsafeB64Encode($input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }
}
