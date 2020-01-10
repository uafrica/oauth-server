<?php

namespace OAuthServer\Model\Storage;

use League\OAuth2\Server\Entity\RefreshTokenEntity;
use League\OAuth2\Server\Storage\RefreshTokenInterface;

class RefreshTokenStorage extends AbstractStorage implements RefreshTokenInterface
{
    /**
     * {@inheritdoc}
     *
     * @param string $token Token
     * @return string
     */
    public function get($token)
    {
        $this->loadModel('OAuthServer.RefreshTokens');
        $result = $this->RefreshTokens->find()
            ->where([
                'refresh_token' => $token,
            ])
            ->first();

        if ($result) {
            $token = (new RefreshTokenEntity($this->server))->setId($result->refresh_token)
                ->setExpireTime($result->expires)
                ->setAccessTokenId($result->oauth_token);

            return $token;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param string $token Token
     * @param int $expireTime Expiry time
     * @param string $accessToken Access token
     * @return void
     */
    public function create($token, $expireTime, $accessToken)
    {
        $this->loadModel('OAuthServer.RefreshTokens');
        $refreshToken = $this->RefreshTokens->newEntity([
            'refresh_token' => $token,
            'oauth_token' => $accessToken,
            'expires' => $expireTime,
        ]);
        $this->RefreshTokens->save($refreshToken);
    }

    /**
     * {@inheritdoc}
     *
     * @param \League\OAuth2\Server\Entity\RefreshTokenEntity $token Refresh token
     * @return void
     */
    public function delete(RefreshTokenEntity $token)
    {
        $this->loadModel('OAuthServer.RefreshTokens');
        $this->RefreshTokens->deleteAll([
            'refresh_token' => $token->getId(),
        ]);
    }
}
