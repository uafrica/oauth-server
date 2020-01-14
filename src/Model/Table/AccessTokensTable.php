<?php

namespace OAuthServer\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasMany;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use OAuthServer\Model\Entity\AccessToken;

/**
 * AccessToken Model
 *
 * @property BelongsTo|OauthClientsTable $OauthClients
 * @property HasMany|AccessTokenScopesTable $AccessTokenScopes
 * @property BelongsToMany|OauthScopesTable $OauthScopes
 *
 * @method AccessToken get($primaryKey, $options = [])
 * @method AccessToken newEntity($data = null, array $options = [])
 * @method AccessToken[] newEntities(array $data, array $options = [])
 * @method AccessToken|bool save(EntityInterface $entity, $options = [])
 * @method AccessToken patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method AccessToken[] patchEntities($entities, array $data, array $options = [])
 * @method AccessToken findOrCreate($search, callable $callback = null, $options = [])
 */
class AccessTokensTable extends Table
{
    /**
     * {@inheritDoc}
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('oauth_access_tokens');
        $this->setPrimaryKey('oauth_token');

        $this->belongsTo('OauthClients', [
            'className' => 'OAuthServer.OauthClients',
            'foreignKey' => 'client_id',
            'propertyName' => 'client',
        ]);
        $this->hasMany('AccessTokenScopes', [
            'className' => 'OAuthServer.AccessTokenScopes',
            'foreignKey' => 'oauth_token',
            'dependant' => true,
        ]);
        $this->belongsToMany('OauthScopes', [
            'className' => 'OAuthServer.OauthScopes',
            'foreignKey' => 'oauth_token',
            'targetForeignKey' => 'scope_id',
            'joinTable' => 'oauth_access_token_scopes',
            'propertyName' => 'scopes',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator->maxLength('oauth_token', 40);
        $validator->boolean('revoked');

        return $validator;
    }

    /**
     * {@inheritDoc}
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['oauth_token']));
        $rules->addCreate($rules->existsIn('client_id', 'OauthClients'));

        return $rules;
    }
}
