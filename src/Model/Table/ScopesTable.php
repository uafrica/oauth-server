<?php

namespace OAuthServer\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Table;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use OAuthServer\Model\Entity\Scope;
use function Functional\map;

/**
 * OAuth 2.0 scopes table
 *
 * @property AccessTokenScopesTable|HasMany $AccessTokenScopes
 * @property AuthCodeScopesTable|HasMany    $AuthCodeScopes
 *
 * @method Scope get($primaryKey, $options = [])
 * @method Scope newEntity($data = null, array $options = [])
 * @method Scope[] newEntities(array $data, array $options = [])
 * @method Scope|bool save(EntityInterface $entity, $options = [])
 * @method Scope patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method Scope[] patchEntities($entities, array $data, array $options = [])
 */
class ScopesTable extends Table implements ScopeRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table('oauth_scopes');
        $this->setPrimaryKey('id'); // @TODO Update after running migrations?
        $this->setEntityClass('OAuthServer.Scope');
        $this->hasMany('AccessTokenScopes', [
            'className' => 'OAuthServer.AccessTokenScopes',
        ]);
        $this->hasMany('AuthCodeScopes', [
            'className' => 'OAuthServer.AuthCodeScopes',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        if ($scope = $this->find()->where([$this->getPrimaryKey() => $identifier])->first()) {
            return $scope->transformToDTO();
        }
        return null;
    }

    /**
     * Dispatches OAuthServer.finalizeScopes event
     *
     * @param array $data
     * @return Event
     */
    protected function dispatchFinalizeScopesEvent(array $data): Event
    {
        $event = new Event('OAuthServer.finalizeScopes', $this, $data);
        $this->getEventManager()->dispatch($event);
        return $event;
    }

    /**
     * Checks whether the given scopes variable obtained from the given event its data is still an array
     *
     * @param Event $event
     * @param mixed $scopes
     * @return void
     * @throws OAuthServerException
     */
    protected function checkIsScopesArrayAfterEvent(Event $event, $scopes): void
    {
        if (!is_array($scopes)) {
            $hint = 'An event handler of event "%s" has mutated scopes event data to something other than an array';
            $hint = sprintf($hint, $event->getName());
            throw new OAuthServerException('The server has failed to handle the request', 1001, 'server_error', 500, $hint);
        }
    }

    /**
     * Check whether the given scopes match with what is available in the scopes table
     *
     * @param ScopeEntityInterface[] $scopes
     * @return void
     * @throws OAuthServerException
     */
    protected function checkScopesExist(array $scopes): void
    {
        $scopes = map($scopes, fn(ScopeEntityInterface $scope) => $scope->getIdentifier());

        /** @var Scope[] $dbEntities */
        $dbEntities = $this
            ->find()
            ->whereInList($this->getPrimaryKey(), $scopes, ['allowEmpty' => true])
            ->all()
            ->toArray();

        $dbScopesFound = map($dbEntities, fn(Scope $dbEntity) => $dbEntity->id);

        foreach ($scopes as $scope) {
            if (!in_array($scope, $dbScopesFound, true)) {
                throw OAuthServerException::invalidScope($scope);
            }
        }
    }

    /**
     * @inheritDoc
     * @throws OAuthServerException
     */
    public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null)
    {
        $event          = $this->dispatchFinalizeScopesEvent([$scopes, $grantType, $clientEntity, $userIdentifier]);
        $externalScopes = $event->getData(0);
        $this->checkIsScopesArrayAfterEvent($event, $externalScopes);
        $this->checkScopesExist($externalScopes);
        return $externalScopes;
    }
}
