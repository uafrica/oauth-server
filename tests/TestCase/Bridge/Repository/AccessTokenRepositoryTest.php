<?php

namespace OAuthServer\Test\TestCase\Bridge\Repository;

use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use OAuthServer\Bridge\Repository\AccessTokenRepository;
use OAuthServer\Bridge\Repository\ClientRepository;
use OAuthServer\Bridge\Repository\ScopeRepository;
use OAuthServer\Model\Entity\AccessToken;
use OAuthServer\Model\Entity\Scope;
use OAuthServer\Model\Table\AccessTokensTable;

class AccessTokenRepositoryTest extends TestCase
{
    public $fixtures = [
        'plugin.OAuthServer.Clients',
        'plugin.OAuthServer.Scopes',
        'plugin.OAuthServer.AccessTokens',
        'plugin.OAuthServer.AccessTokenScopes',
    ];

    /**
     * @var AccessTokenRepository
     */
    private $repository;

    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * @var ScopeRepository
     */
    private $scopeRepository;

    /**
     * @var AccessTokensTable
     */
    private $AccessTokens;

    public function setUp()
    {
        parent::setUp();
        $this->repository = new AccessTokenRepository();
        $this->clientRepository = new ClientRepository();
        $this->scopeRepository = new ScopeRepository();
        $this->AccessTokens = TableRegistry::getTableLocator()->get('OAuthServer.AccessTokens');
    }

    public function tearDown()
    {
        unset($this->repository, $this->clientRepository, $this->scopeRepository, $this->AccessTokens);
        FrozenTime::setTestNow();
        parent::tearDown();
    }

    public function testGetNewToken()
    {
        FrozenTime::setTestNow('2020-01-01 00:00:00');
        $client = $this->clientRepository->getClientEntity('TEST');
        $scopes = [
            $this->scopeRepository->getScopeEntityByIdentifier('test'),
        ];
        $userId = 'user1';

        $token = $this->repository->getNewToken($client, $scopes, $userId);

        $this->assertInstanceOf(AccessToken::class, $token);
        $this->assertSame('TEST', $token->getClient()->getIdentifier());
        $this->assertSame('TEST', $token->client_id);
        $this->assertSame('user1', $token->getUserIdentifier());
        $this->assertSame(['test'], collection($token->getScopes())->extract(static function (Scope $scope) {
            return $scope->getIdentifier();
        })->toList());
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function testPersistNewAccessToken()
    {
        FrozenTime::setTestNow('2020-01-01 00:00:00');
        $token = new AccessToken();
        $token->setIdentifier('access_token_1');
        $token->setClient($this->clientRepository->getClientEntity('TEST'));
        $token->setUserIdentifier('user1');
        $token->setExpiryDateTime(FrozenTime::now()->addHour());
        $token->addScope($this->scopeRepository->getScopeEntityByIdentifier('test'));

        $this->repository->persistNewAccessToken($token);

        $saved = $this->AccessTokens->get($token->getIdentifier(), ['contain' => ['OauthScopes', 'OauthClients']]);

        $this->assertSame('access_token_1', $saved->getIdentifier());
        $this->assertSame('TEST', $saved->getClient()->getIdentifier());
        $this->assertSame('user1', $saved->getUserIdentifier());
        $this->assertSame(['test'], collection($saved->getScopes())->extract(static function (Scope $scope) {
            return $scope->getIdentifier();
        })->toList());
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function testPersistNewAccessTokenConflict()
    {
        FrozenTime::setTestNow('2020-01-01 00:00:00');
        $token = $this->AccessTokens->newEntity([]);
        $token->setIdentifier('exist_token_1');
        $token->setClient($this->clientRepository->getClientEntity('TEST'));
        $token->setUserIdentifier('user1');
        $token->setExpiryDateTime(FrozenTime::now()->addHour());
        $token->addScope($this->scopeRepository->getScopeEntityByIdentifier('test'));

        $this->expectException(UniqueTokenIdentifierConstraintViolationException::class);
        $this->repository->persistNewAccessToken($token);

        debug($this->AccessTokens->find()->count());
    }

    public function testRevokeAccessToken()
    {
        $this->repository->revokeAccessToken('exist_token_1');

        $token = $this->AccessTokens->get('exist_token_1');
        $this->assertTrue($token->revoked);
    }

    public function testIsAccessTokenRevoked()
    {
        $this->assertFalse($this->repository->isAccessTokenRevoked('exist_token_1'));

        $this->repository->revokeAccessToken('exist_token_1');

        $this->assertTrue($this->repository->isAccessTokenRevoked('exist_token_1'));
    }
}
