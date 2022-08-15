<?php

namespace OAuthServer\Test\TestCase\Controller\Component;

use Cake\TestSuite\TestCase;
use OAuthServer\Controller\Component\OAuthComponent;
use OAuthServer\Controller\OAuthController;
use OAuthServer\Lib\Data\Entity\AccessToken;
use OAuthServer\Lib\Data\Entity\Scope;
use OAuthServer\Lib\Data\Entity\User;
use OAuthServer\Lib\Enum\Repository;
use OAuthServer\Model\Table\ClientsTable;
use DateTimeImmutable;
use OAuthServer\Plugin;

class OAuthComponentTest extends TestCase
{
    /**
     * @inheritDoc
     */
    public $fixtures = [
        'plugin.OAuthServer.Clients',
        'plugin.OAuthServer.Users',
        'plugin.OAuthServer.Scopes',
        'plugin.OAuthServer.AccessTokens',
        'plugin.OAuthServer.AccessTokenScopes',
    ];

    /**
     * @var OAuthComponent
     */
    protected OAuthComponent $component;

    /**
     * @var ClientsTable
     */
    protected ClientsTable $clientsTable;

    /**
     * @inheritDoc
     */
    public function setUp()
    {
        parent::setUp();
        $controller = new OAuthController();
        $controller->initialize();
        $this->component    = new OAuthComponent($controller->components());
        $this->clientsTable = $this->component->loadRepository('Clients', Repository::CLIENT());
    }

    /**
     * @return void
     */
    public function testGetSessionUserId(): void
    {
        $this->assertNull($this->component->getSessionUserId());
        $this->component->getController()->Auth->storage()->write(['id' => 5]);
        $this->assertEquals(5, $this->component->getSessionUserId());
    }

    /**
     * @return void
     */
    public function testGetSessionUserData(): void
    {
        $this->assertNull($this->component->getSessionUserData());
        $this->component->getController()->Auth->storage()->write(['id' => 5]);
        $this->assertInstanceOf(User::class, $this->component->getSessionUserData());
    }

    /**
     * @return void
     */
    public function testHasActiveAccessTokens(): void
    {
        $clientId = 'TEST';
        $userId   = 1;
        $this->assertFalse($this->component->hasActiveAccessTokens($clientId, $userId));
        $this->assertFalse($this->component->hasActiveAccessTokens($clientId));
        $data = new AccessToken();
        $data->setIdentifier('123');
        $data->setClient($this->clientsTable->getClientEntity($clientId));
        $data->setUserIdentifier(1);
        $data->setPrivateKey(Plugin::instance()->getPrivateKey());
        $data->setExpiryDateTime(new DateTimeImmutable('+1 day'));
        $this->component->AccessTokens->persistNewAccessToken($data);
        $this->assertTrue($this->component->hasActiveAccessTokens($clientId, $userId));
    }

    /**
     * @return void
     */
    public function testEnrichScopes(): void
    {
        $scope  = $this->component->Scopes->getScopeEntityByIdentifier('test');
        $scopes = [$scope];
        $this->assertNull($this->component->enrichScopes(...$scopes));
        $this->assertEquals('test', $scope->getIdentifier());
        $this->assertInstanceOf(Scope::class, $scope);
        $this->assertEquals('Default scope', $scope->getDescription());
    }
}