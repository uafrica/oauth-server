<?php

namespace OAuthServer\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use OAuthServer\Model\Entity\RefreshToken;

/**
 * RefreshToken Model
 *
 * @property BelongsTo|AccessTokensTable $AccessTokens
 *
 * @method RefreshToken get($primaryKey, $options = [])
 * @method RefreshToken newEntity($data = null, array $options = [])
 * @method RefreshToken[] newEntities(array $data, array $options = [])
 * @method RefreshToken|bool save(EntityInterface $entity, $options = [])
 * @method RefreshToken patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method RefreshToken[] patchEntities($entities, array $data, array $options = [])
 * @method RefreshToken findOrCreate($search, callable $callback = null, $options = [])
 */
class RefreshTokensTable extends Table
{
    /**
     * {@inheritDoc}
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('oauth_refresh_tokens');
        $this->setPrimaryKey('refresh_token');

        $this->belongsTo('AccessTokens', [
            'className' => 'OAuthServer.AccessTokens',
            'foreignKey' => 'oauth_token',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator->maxLength('refresh_token', 40);
        $validator->boolean('revoked');

        return $validator;
    }

    /**
     * @param RulesChecker $rules the rules
     * @return RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['refresh_token']));

        return $rules;
    }
}
