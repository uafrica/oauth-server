<?php

namespace OAuthServer\Test\TestCase\Bridge\Repository;

use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use OAuthServer\Bridge\Repository\AuthCodeRepository;
use OAuthServer\Bridge\Repository\ClientRepository;
use OAuthServer\Bridge\Repository\ScopeRepository;
use OAuthServer\Model\Entity\AuthCode;
use OAuthServer\Model\Entity\Scope;
use OAuthServer\Model\Table\AuthCodesTable;

class AuthCodeRepositoryTest extends TestCase
{
    public $fixtures = [
        'plugin.OAuthServer.Clients',
        'plugin.OAuthServer.Scopes',
        'plugin.OAuthServer.AuthCodes',
        'plugin.OAuthServer.AuthCodeScopes',
    ];

    /**
     * @var AuthCodeRepository
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
     * @var AuthCodesTable
     */
    private $AuthCodes;

    public function setUp()
    {
        parent::setUp();
        $this->repository = new AuthCodeRepository();
        $this->clientRepository = new ClientRepository();
        $this->scopeRepository = new ScopeRepository();
        $this->AuthCodes = TableRegistry::getTableLocator()->get('OAuthServer.AuthCodes');
    }

    public function tearDown()
    {
        unset($this->repository, $this->clientRepository, $this->scopeRepository, $this->AuthCodes);
        FrozenTime::setTestNow();
        parent::tearDown();
    }

    public function testGetNewAuthCode()
    {
        $authCode = $this->repository->getNewAuthCode();

        $this->assertInstanceOf(AuthCode::class, $authCode);
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function testPersistNewAuthCode()
    {
        FrozenTime::setTestNow('2020-01-01 00:00:00');
        $authCode = new AuthCode();
        $authCode->setIdentifier('auth_code_1');
        $authCode->setClient($this->clientRepository->getClientEntity('TEST'));
        $authCode->setUserIdentifier('user1');
        $authCode->setExpiryDateTime(FrozenTime::now()->addHour());
        $authCode->setRedirectUri('https://example.com');
        $authCode->addScope($this->scopeRepository->getScopeEntityByIdentifier('test'));

        $this->repository->persistNewAuthCode($authCode);

        $saved = $this->AuthCodes->get($authCode->getIdentifier(), ['contain' => ['OauthScopes', 'OauthClients']]);

        $this->assertSame('auth_code_1', $saved->getIdentifier());
        $this->assertSame('TEST', $saved->getClient()->getIdentifier());
        $this->assertSame('user1', $saved->getUserIdentifier());
        $this->assertSame('https://example.com', $saved->getRedirectUri());
        $this->assertSame(['test'], collection($saved->getScopes())->extract(static function (Scope $scope) {
            return $scope->getIdentifier();
        })->toList());
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function testPersistNewAuthCodeConflict()
    {
        FrozenTime::setTestNow('2020-01-01 00:00:00');
        $authCode = $this->AuthCodes->newEntity([]);
        $authCode->setIdentifier('exist_code_1');
        $authCode->setClient($this->clientRepository->getClientEntity('TEST'));
        $authCode->setUserIdentifier('user1');
        $authCode->setExpiryDateTime(FrozenTime::now()->addHour());
        $authCode->addScope($this->scopeRepository->getScopeEntityByIdentifier('test'));

        $this->expectException(UniqueTokenIdentifierConstraintViolationException::class);
        $this->repository->persistNewAuthCode($authCode);

        debug($this->AuthCodes->find()->count());
    }

    public function testRevokeAuthCode()
    {
        $this->repository->revokeAuthCode('exist_code_1');

        $token = $this->AuthCodes->get('exist_code_1');
        $this->assertTrue($token->revoked);
    }

    public function testIsAuthCodeRevoked()
    {
        $this->assertFalse($this->repository->isAuthCodeRevoked('exist_code_1'));

        $this->repository->revokeAuthCode('exist_code_1');

        $this->assertTrue($this->repository->isAuthCodeRevoked('exist_code_1'));
    }
}
