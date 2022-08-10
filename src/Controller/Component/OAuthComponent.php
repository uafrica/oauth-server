<?php

namespace OAuthServer\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Component\AuthComponent;
use Cake\ORM\Table;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use OAuthServer\Lib\Data\Entity\Scope as ScopeData;
use OAuthServer\Lib\Data\Entity\User as UserData;
use OAuthServer\Lib\Enum\Repository;
use OAuthServer\Lib\Traits\RepositoryAwareTrait;
use OAuthServer\Model\Table\AccessTokensTable;
use OAuthServer\Model\Table\ScopesTable;
use LogicException;
use function Functional\map;

/**
 * OAuth 2.0 server process controller helper component
 *
 * @property AccessTokensTable $AccessTokens
 * @property Table             $Users
 * @property ScopesTable       $Scopes
 */
class OAuthComponent extends Component
{
    use RepositoryAwareTrait;

    /**
     * @inheritDoc
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->loadRepository('AccessTokens', Repository::ACCESS_TOKEN());
        $this->loadRepository('Users', Repository::USER());
        $this->loadRepository('Scopes', Repository::USER());
    }

    /**
     * Get the user id based on the current AuthComponent session
     *
     * @return string|null
     * @throws LogicException
     */
    public function getSessionUserId(): ?string
    {
        $userId     = null;
        $components = $this->getController()->components();
        if ($components->has('Auth')) {
            $component = $components->get('Auth');
            if ($component instanceof AuthComponent) {
                if (!method_exists($this->Users, 'getPrimaryKey')) {
                    throw new LogicException('missing primary key retrieval method');
                }
                $userId = $component->user($this->Users->getPrimaryKey());
            }
        }
        return $userId;
    }

    /**
     * Get the OAuth 2.0 server user DTO object based on the current user session
     *
     * @return UserData|null
     */
    public function getSessionUserData(): ?UserData
    {
        if ($userId = $this->getSessionUserId()) {
            $data = new UserData();
            $data->setIdentifier($userId);
            return $data;
        }
        return null;
    }

    /**
     * Check if the current client+user has active access tokens
     *
     * @param string      $clientId
     * @param string|null $userId
     * @return bool True if active access tokens
     */
    public function hasActiveAccessTokens(string $clientId, ?string $userId): bool
    {
        $options = ['client_id' => $clientId, 'user_id' => $userId];
        return !!$this->AccessTokens->find('active', $options)->count();
    }

    /**
     * Enriches the given OAuth 2.0 server scope data object implementations
     * with their aditionally plugin provided descriptions
     *
     * @param ScopeEntityInterface ...$scopes
     * @return void
     */
    public function enrichScopes(ScopeEntityInterface ...$scopes): void
    {
        $ids        = array_unique(map($scopes, fn(ScopeEntityInterface $scope) => $scope->getIdentifier()));
        $primaryKey = $this->Scopes->getPrimaryKey();
        $entities   = $this->Scopes
            ->find()
            ->whereInList($primaryKey, $ids, ['allowEmpty' => true])
            ->all()
            ->indexBy($primaryKey);
        foreach ($scopes as $scope) {
            $id = $scope->getIdentifier();
            if ($scope instanceof ScopeData && isset($entities[$id]->description)) {
                $scope->setDescription($entities[$id]->description);
            }
        }
    }
}