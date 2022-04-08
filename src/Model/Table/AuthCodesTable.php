<?php

namespace OAuthServer\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasMany;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use OAuthServer\Model\Entity\AuthCode;

/**
 * AuthCode Model
 *
 * @property BelongsTo|OauthClientsTable $OauthClients
 * @property HasMany|OauthAuthCodeScopesTable $AuthCodeScopes
 * @property BelongsToMany|OauthScopesTable $OauthScopes
 *
 * @method AuthCode get($primaryKey, $options = [])
 * @method AuthCode newEntity($data = null, array $options = [])
 * @method AuthCode[] newEntities(array $data, array $options = [])
 * @method AuthCode|bool save(EntityInterface $entity, $options = [])
 * @method AuthCode patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method AuthCode[] patchEntities($entities, array $data, array $options = [])
 * @method AuthCode findOrCreate($search, callable $callback = null, $options = [])
 */
class AuthCodesTable extends Table implements RevocableTokensTableInterface
{
    use RevocableTokensTableTrait;

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('oauth_auth_codes');
        $this->setPrimaryKey('code');

        $this->belongsTo('OauthClients', [
            'className' => 'OAuthServer.OauthClients',
            'foreignKey' => 'client_id',
            'propertyName' => 'client',
        ]);
        $this->hasMany('AuthCodeScopes', [
            'className' => 'OAuthServer.OauthAuthCodeScopes',
            'foreignKey' => 'auth_code',
            'dependant' => true,
        ]);
        $this->belongsToMany('OauthScopes', [
            'className' => 'OAuthServer.OauthScopes',
            'foreignKey' => 'auth_code',
            'targetForeignKey' => 'scope_id',
            'joinTable' => 'oauth_auth_code_scopes',
            'propertyName' => 'scopes',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator->maxLength('code', 40);
        $validator->boolean('revoked');

        return $validator;
    }

    /**
     * {@inheritDoc}
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['code']));
        $rules->addCreate($rules->existsIn('client_id', 'OauthClients'));

        return $rules;
    }
}
