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
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use OAuthServer\Model\Entities\AccessTokenEntity;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $AccessTokens = TableRegistry::get('OAuthServer.AccessTokens');
        $AuthSessions = TableRegistry::get('OAuthServer.Sessions');

        $ownerID = $accessTokenEntity->getUserIdentifier();
        $AuthSession = $AuthSessions->find()
        ->where(['owner_id'=> $ownerID])->last();
        $token = $accessTokenEntity->getIdentifier();
        $expires = $accessTokenEntity->getExpiryDateTime()->getTimestamp();

        $accessToken = $AccessTokens->newEntity([
            'oauth_token' => $token,
            'expires' => $expires,
            'session_id' => $AuthSession->id
        ]);
        $AccessTokens->save($accessToken);
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAccessToken($tokenId)
    {
        $AccessTokens = TableRegistry::get('OAuthServer.AccessTokens');
        $oldAccessToken = $AccessTokens->find()
        ->where(['oauth_token' => $tokenId])->first();
        $AccessTokens->delete($oldAccessToken);
    }

    /**
     * {@inheritdoc}
     */
    public function isAccessTokenRevoked($tokenId)
    {
        $AccessTokens = TableRegistry::get('OAuthServer.AccessTokens');
        $exists = $AccessTokens->exists(['oauth_token' => $tokenId]);
        return !$exists;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $accessToken = new AccessTokenEntity();
        $accessToken->setClient($clientEntity);
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }
        $accessToken->setUserIdentifier($userIdentifier);

        return $accessToken;
    }
}
