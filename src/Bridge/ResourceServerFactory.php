<?php

namespace OAuthServer\Bridge;

use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\ResourceServer;
use OAuthServer\Bridge\Repository\AccessTokenRepository;

class ResourceServerFactory
{
    /**
     * @var AccessTokenRepositoryInterface
     */
    private $accessTokenRepository;

    /**
     * @var string
     */
    private $publicKeyPath;

    /**
     * ResourceServerFactory constructor.
     *
     * @param string $publicKeyPath the public key path
     */
    public function __construct($publicKeyPath)
    {
        $this->setPublicKeyPath($publicKeyPath);
    }

    /**
     * @return ResourceServer
     */
    public function create(): ResourceServer
    {
        return new ResourceServer($this->getAccessTokenRepository(), $this->getPublicKeyPath());
    }

    /**
     * @return AccessTokenRepositoryInterface
     */
    public function getAccessTokenRepository(): AccessTokenRepositoryInterface
    {
        if (!$this->accessTokenRepository) {
            $this->accessTokenRepository = new AccessTokenRepository();
        }

        return $this->accessTokenRepository;
    }

    /**
     * @param AccessTokenRepositoryInterface $accessTokenRepository the AccessTokenRepository
     * @return void
     */
    public function setAccessTokenRepository(AccessTokenRepositoryInterface $accessTokenRepository): void
    {
        $this->accessTokenRepository = $accessTokenRepository;
    }

    /**
     * @return string
     */
    public function getPublicKeyPath(): string
    {
        return $this->publicKeyPath;
    }

    /**
     * @param string $publicKeyPath the public key path
     * @return void
     */
    public function setPublicKeyPath(string $publicKeyPath): void
    {
        $this->publicKeyPath = $publicKeyPath;
    }
}
