<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace OAuthServer\Model\Repositories;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use OAuthServer\Model\Entities\AuthCodeEntity;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        $AuthCodes = TableRegistry::get('OAuthServer.AuthCodes');
        $AuthSessions = TableRegistry::get('OAuthServer.Sessions');

        $clientID = $authCodeEntity->getClient()->getIdentifier();
        $ownerID = $authCodeEntity->getUserIdentifier();
        $redirectUri = $authCodeEntity->getClient()->getRedirectUri();
        $code = $authCodeEntity->getIdentifier();
        $expires = $authCodeEntity->getExpiryDateTime()->getTimestamp();

        // Create session
        $session = $AuthSessions->newEntity([
            'owner_model' => 'Users',
            'owner_id' => $ownerID,
            'client_id' => $clientID,
            'client_redirect_uri' => $redirectUri
        ]);
        $session = $AuthSessions->save($session);

        // Create token
        $AuthCode = $AuthCodes->newEntity([
            'code' => $code,
            'redirect_uri' => $redirectUri,
            'expires' => $expires,
            'session_id' => $session->id
        ]);
        $AuthCodes->save($AuthCode);

        return ;

        // Some logic to persist the auth code to a database
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAuthCode($codeId)
    {
        // Some logic to revoke the auth code in a database
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthCodeRevoked($codeId)
    {
        return false; // The auth code has not been revoked
    }

    /**
     * {@inheritdoc}
     */
    public function getNewAuthCode()
    {
        return new AuthCodeEntity();
    }
}
