<?php

namespace OAuthServer\Model\Table;

use Cake\ORM\Association\BelongsTo;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;

/**
 * Class AccessTokenScopesTable
 *
 * @property BelongsTo|AccessTokensTable $AccessTokens
 * @property BelongsTo|OauthScopesTable $OauthScopes
 */
class AccessTokenScopesTable extends Table
{
    /**
     * {@inheritDoc}
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('oauth_access_token_scopes');
        $this->setPrimaryKey(['oauth_token', 'scope_id']);

        $this->belongsTo('AccessTokens', [
            'className' => 'OAuthServer.AccessTokens',
            'foreignKey' => 'oauth_token',
        ]);
        $this->belongsTo('OauthScopes', [
            'className' => 'OAuthServer.OauthScopes',
            'foreignKey' => 'scope_id',
            'propertyName' => 'scopes',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->isUnique(['oauth_token', 'scope_id']);
        $rules->addCreate($rules->existsIn('oauth_token', 'AccessTokens'));
        $rules->addCreate($rules->existsIn('scope_id', 'OauthScopes'));

        return $rules;
    }
}
