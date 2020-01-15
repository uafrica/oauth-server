<?php

namespace OAuthServer\Bridge\Repository;

use Cake\Datasource\ModelAwareTrait;
use Cake\Event\EventDispatcherInterface;
use Cake\Event\EventDispatcherTrait;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use OAuthServer\Model\Table\OauthScopesTable;

class ScopeRepository implements ScopeRepositoryInterface, EventDispatcherInterface
{
    use EventDispatcherTrait;
    use ModelAwareTrait;

    /**
     * @var OauthScopesTable
     */
    private $table;

    /**
     * RefreshTokenRepository constructor.
     */
    public function __construct()
    {
        $this->table = $this->loadModel('OAuthServer.OauthScopes');
    }

    /**
     * {@inheritDoc}
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        return $this->table->find()->where([$this->table->getPrimaryKey() => $identifier])->first();
    }

    /**
     * {@inheritDoc}
     */
    public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null)
    {
        $result = $this->dispatchEvent('OAuthServer.finalizeScopes', compact(
            'scopes',
            'grantType',
            'clientEntity',
            'userIdentifier'
        ));

        if ($result->getResult()) {
            $scopes = $result->getResult();
        }

        return (array)$scopes;
    }
}
