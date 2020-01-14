<?php

namespace OAuthServer\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use OAuthServer\Model\Entity\Scope;

/**
 * Scope Model
 *
 * @property HasMany|AccessTokenScopesTable $AccessTokenScopes
 * @property HasMany|AuthCodeScopesTable $AuthCodeScopes
 *
 * @method Scope get($primaryKey, $options = [])
 * @method Scope newEntity($data = null, array $options = [])
 * @method Scope[] newEntities(array $data, array $options = [])
 * @method Scope|bool save(EntityInterface $entity, $options = [])
 * @method Scope patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method Scope[] patchEntities($entities, array $data, array $options = [])
 * @method Scope findOrCreate($search, callable $callback = null, $options = [])
 */
class OauthScopesTable extends Table
{
    /**
     * {@inheritDoc}
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('oauth_scopes');
        $this->setPrimaryKey('id');
        $this->setEntityClass(Scope::class);

        $this->hasMany('AccessTokenScopes', [
            'className' => 'OAuthServer.AccessTokenScopes',
        ]);
        $this->hasMany('AuthCodeScopes', [
            'className' => 'OAuthServer.AuthCodeScopes',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->maxLength('id', 40)
            ->requirePresence('id');
        $validator
            ->maxLength('description', 200)
            ->allowEmpty('description');

        return $validator;
    }
}
