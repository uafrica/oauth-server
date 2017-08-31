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
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use OAuthServer\Model\Entities\RefreshTokenEntity;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntityInterface)
    {
        $RefreshTokens = TableRegistry::get('OAuthServer.RefreshTokens');
        $refreshToken = $refreshTokenEntityInterface->getIdentifier();
        $token = $refreshTokenEntityInterface
        ->getAccessToken()
        ->getIdentifier();
        $expires = $refreshTokenEntityInterface->getExpiryDateTime()->getTimestamp();

        $refreshToken = $RefreshTokens->newEntity([
            'oauth_token' => $token,
            'refresh_token' => $refreshToken,
            'expires' => $expires
        ]);
        $RefreshTokens->save($refreshToken);
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function revokeRefreshToken($tokenId)
    {
        // Some logic to revoke the refresh token in a database
    }

    /**
     * {@inheritdoc}
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        return false; // The refresh token has not been revoked
    }

    /**
     * {@inheritdoc}
     */
    public function getNewRefreshToken()
    {
        return new RefreshTokenEntity();
    }
}
