<?php

namespace OAuthServer\Test\TestCase\Bridge\Repository;

use Cake\TestSuite\TestCase;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use OAuthServer\Bridge\Repository\ScopeRepository;
use OAuthServer\Model\Entity\Client;
use OAuthServer\Model\Entity\Scope;

class ScopeRepositoryTest extends TestCase
{
    public $fixtures = [
        'plugin.OAuthServer.Scopes',
    ];

    /**
     * @var ScopeRepository
     */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->repository = new ScopeRepository();
    }

    public function tearDown()
    {
        unset($this->repository);
        parent::tearDown();
    }

    public function testGetScopeEntityByIdentifier()
    {
        $entity = $this->repository->getScopeEntityByIdentifier('test');

        $this->assertInstanceOf(ScopeEntityInterface::class, $entity);
        $this->assertSame('test', $entity->getIdentifier());
    }

    public function testFinalizeScopes()
    {
        $clientEntity = new Client();
        $scopes = [];
        $scopes[] = new Scope(['id' => '*']);

        $results = $this->repository->finalizeScopes($scopes, 'foo', $clientEntity);

        $this->assertSame($scopes, $results);
    }

    public function testFinalizeScopesWithEmptyScopes()
    {
        $clientEntity = new Client();

        $results = $this->repository->finalizeScopes([], 'foo', $clientEntity);

        $this->assertSame([], $results);
    }
}
