<?php

namespace OAuthServer\Bridge\Repository;

use Cake\Datasource\RepositoryInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use OAuthServer\Bridge\Entity\User;
use OAuthServer\Bridge\UserFinderByUserCredentialsInterface;

/**
 * implemented UserRepositoryInterface
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * @var RepositoryInterface
     */
    private $finder;

    /**
     * UserRepository constructor.
     *
     * @param UserFinderByUserCredentialsInterface $finder user finder
     */
    public function __construct(UserFinderByUserCredentialsInterface $finder)
    {
        $this->finder = $finder;
    }

    /**
     * {@inheritDoc}
     */
    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity)
    {
        $user = $this->finder->findUser($username, $password);

        return $user ? new User($user->get($this->finder->getPrimaryKey())) : null;
    }

    /**
     * @param RepositoryInterface $finder the User's table
     * @return void
     */
    public function setFinder(RepositoryInterface $finder): void
    {
        $this->finder = $finder;
    }
}
