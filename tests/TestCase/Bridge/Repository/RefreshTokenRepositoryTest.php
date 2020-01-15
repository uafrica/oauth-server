<?php

namespace OAuthServer\Test\TestCase\Bridge\Repository;

use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use OAuthServer\Bridge\Repository\AccessTokenRepository;
use OAuthServer\Bridge\Repository\RefreshTokenRepository;
use OAuthServer\Model\Entity\AccessToken;
use OAuthServer\Model\Entity\RefreshToken;
use OAuthServer\Model\Table\RefreshTokensTable;

class RefreshTokenRepositoryTest extends TestCase
{
    public $fixtures = [
        'plugin.OAuthServer.Clients',
        'plugin.OAuthServer.Scopes',
        'plugin.OAuthServer.AccessTokens',
        'plugin.OAuthServer.RefreshTokens',
    ];

    /**
     * @var RefreshTokenRepository
     */
    private $repository;

    /**
     * @var AccessTokenRepository
     */
    private $accessTokenRepository;

    /**
     * @var RefreshTokensTable
     */
    private $RefreshTokens;

    public function setUp()
    {
        parent::setUp();
        $this->repository = new RefreshTokenRepository();
        $this->accessTokenRepository = new AccessTokenRepository();
        $this->RefreshTokens = TableRegistry::getTableLocator()->get('OAuthServer.RefreshTokens');
    }

    public function tearDown()
    {
        unset($this->repository, $this->clientRepository, $this->scopeRepository, $this->RefreshTokens);
        FrozenTime::setTestNow();
        parent::tearDown();
    }

    public function testGetNewToken()
    {
        FrozenTime::setTestNow('2020-01-01 00:00:00');

        $token = $this->repository->getNewRefreshToken();

        $this->assertInstanceOf(RefreshToken::class, $token);
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function testPersistNewRefreshToken()
    {
        FrozenTime::setTestNow('2020-01-01 00:00:00');
        $accessToken = new AccessToken();
        $accessToken->setIdentifier('exist_token_1');
        $token = $this->repository->getNewRefreshToken();
        $token->setIdentifier('refresh_token_1');
        $token->setExpiryDateTime(FrozenTime::now()->addHour());
        $token->setAccessToken($accessToken);

        $this->repository->persistNewRefreshToken($token);

        $saved = $this->RefreshTokens->get($token->getIdentifier(), ['contain' => ['AccessTokens']]);

        $this->assertSame('refresh_token_1', $saved->getIdentifier());
        $this->assertSame('exist_token_1', $saved->getAccessToken()->getIdentifier());
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function testPersistNewRefreshTokenConflict()
    {
        FrozenTime::setTestNow('2020-01-01 00:00:00');
        $accessToken = new AccessToken();
        $accessToken->setIdentifier('exist_token_1');
        $token = $this->repository->getNewRefreshToken();
        $token->setIdentifier('exist_refresh_token_1');
        $token->setExpiryDateTime(FrozenTime::now()->addHour());
        $token->setAccessToken($accessToken);

        $this->expectException(UniqueTokenIdentifierConstraintViolationException::class);
        $this->repository->persistNewRefreshToken($token);

        debug($this->RefreshTokens->find()->count());
    }

    public function testRevokeRefreshToken()
    {
        $this->repository->revokeRefreshToken('exist_refresh_token_1');

        $token = $this->RefreshTokens->get('exist_refresh_token_1');
        $this->assertTrue($token->revoked);
    }

    public function testIsRefreshTokenRevoked()
    {
        $this->assertFalse($this->repository->isRefreshTokenRevoked('exist_refresh_token_1'));

        $this->repository->revokeRefreshToken('exist_refresh_token_1');

        $this->assertTrue($this->repository->isRefreshTokenRevoked('exist_refresh_token_1'));
    }
}
