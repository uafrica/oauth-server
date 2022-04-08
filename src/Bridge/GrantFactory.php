<?php

namespace OAuthServer\Bridge;

use Cake\Utility\Inflector;
use DateInterval;
use Exception;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\GrantTypeInterface;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use OAuthServer\Bridge\Repository\AuthCodeRepository;
use OAuthServer\Bridge\Repository\RefreshTokenRepository;
use OAuthServer\Bridge\Repository\UserRepository;

class GrantFactory
{
    /**
     * @var UserFinderByUserCredentialsInterface
     */
    private $userFinder;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var RefreshTokenRepositoryInterface
     */
    private $refreshTokenRepository;

    /**
     * @var AuthCodeRepositoryInterface
     */
    private $authCodeRepository;

    /**
     * @var string
     */
    private $authCodeTTL = 'PT10M';

    /**
     * GrantFactory constructor.
     *
     * @param UserFinderByUserCredentialsInterface $userFinder a User finder for PasswordGrant
     */
    public function __construct(UserFinderByUserCredentialsInterface $userFinder)
    {
        $this->userFinder = $userFinder;
    }

    /**
     * @return string
     */
    public function getAuthCodeTTL(): string
    {
        return $this->authCodeTTL;
    }

    /**
     * @param string $authCodeTTL TTL as DateInterval format
     * @return void
     */
    public function setAuthCodeTTL(string $authCodeTTL): void
    {
        $this->authCodeTTL = $authCodeTTL;
    }

    /**
     * @return AuthCodeRepositoryInterface
     */
    public function getAuthCodeRepository(): AuthCodeRepositoryInterface
    {
        if (!$this->authCodeRepository) {
            $this->authCodeRepository = new AuthCodeRepository();
        }

        return $this->authCodeRepository;
    }

    /**
     * @param AuthCodeRepositoryInterface $authCodeRepository the AuthCode repository
     * @return void
     */
    public function setAuthCodeRepository(AuthCodeRepositoryInterface $authCodeRepository): void
    {
        $this->authCodeRepository = $authCodeRepository;
    }

    /**
     * @return UserRepositoryInterface
     */
    public function getUserRepository(): UserRepositoryInterface
    {
        if (!$this->userRepository) {
            $this->userRepository = new UserRepository($this->userFinder);
        }

        return $this->userRepository;
    }

    /**
     * @param UserRepositoryInterface $userRepository the repository
     * @return void
     */
    public function setUserRepository(UserRepositoryInterface $userRepository): void
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @return RefreshTokenRepositoryInterface
     */
    public function getRefreshTokenRepository(): RefreshTokenRepositoryInterface
    {
        if (!$this->refreshTokenRepository) {
            $this->refreshTokenRepository = new RefreshTokenRepository();
        }

        return $this->refreshTokenRepository;
    }

    /**
     * @param RefreshTokenRepositoryInterface $refreshTokenRepository the repository
     * @return void
     */
    public function setRefreshTokenRepository(RefreshTokenRepositoryInterface $refreshTokenRepository): void
    {
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    /**
     * @param string $grantType eg: 'AuthCode', 'RefreshToken', 'ClientCredentials', 'Password'
     * @return GrantTypeInterface
     */
    public function create($grantType): GrantTypeInterface
    {
        $grant = Inflector::camelize($grantType);

        if (method_exists($this, 'create' . $grant)) {
            return $this->{'create' . $grant}();
        }

        return $this->createDefault($grant);
    }

    /**
     * @return PasswordGrant
     */
    private function createPassword(): PasswordGrant
    {
        return new PasswordGrant($this->getUserRepository(), $this->getRefreshTokenRepository());
    }

    /**
     * @return AuthCodeGrant
     * @throws Exception
     */
    private function createAuthCode(): AuthCodeGrant
    {
        return new AuthCodeGrant(
            $this->getAuthCodeRepository(),
            $this->getRefreshTokenRepository(),
            new DateInterval($this->getAuthCodeTTL())
        );
    }

    /**
     * @return RefreshTokenGrant
     */
    private function createRefreshToken(): RefreshTokenGrant
    {
        return new RefreshTokenGrant($this->getRefreshTokenRepository());
    }

    /**
     * @param string $grant class name
     * @return GrantTypeInterface
     */
    private function createDefault($grant): GrantTypeInterface
    {
        $className = '\\League\\OAuth2\\Server\\Grant\\' . $grant . 'Grant';

        return new $className();
    }
}
