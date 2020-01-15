<?php

namespace OAuthServer\Test\TestCase\Bridge\Repository;

use Cake\ORM\Entity;
use OAuthServer\Bridge\Entity\User;
use OAuthServer\Bridge\Repository\UserRepository;
use OAuthServer\Bridge\UserFinderByUserCredentialsInterface;
use OAuthServer\Model\Entity\Client;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    /**
     * @var UserFinderByUserCredentialsInterface|MockObject
     */
    private $finder;

    /**
     * @var UserRepository
     */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->finder = $this->getMockBuilder(UserFinderByUserCredentialsInterface::class)->getMock();
        $this->repository = new UserRepository($this->finder);
    }

    public function tearDown()
    {
        unset($this->repository, $this->finder);
        parent::tearDown();
    }

    public function testGetUserEntityByUserCredentials()
    {
        $username = 'foo@example.com';
        $password = '123456';
        $grantType = 'password';
        $client = new Client();

        $this->finder
            ->expects($this->once())
            ->method('findUser')
            ->with($this->identicalTo($username), $this->identicalTo($password))
            ->willReturn(new Entity(['id' => 'user1']));

        $this->finder
            ->expects($this->once())
            ->method('getPrimaryKey')
            ->willReturn('id');

        $result = $this->repository->getUserEntityByUserCredentials($username, $password, $grantType, $client);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame('user1', $result->getIdentifier());
    }

    public function testGetUserEntityByUserCredentialsAsNotFound()
    {
        $username = 'foo@example.com';
        $password = '123456';
        $grantType = 'password';
        $client = new Client();

        $this->finder
            ->expects($this->once())
            ->method('findUser')
            ->with($this->identicalTo($username), $this->identicalTo($password))
            ->willReturn(null);

        $result = $this->repository->getUserEntityByUserCredentials($username, $password, $grantType, $client);

        $this->assertNull($result);
    }
}
