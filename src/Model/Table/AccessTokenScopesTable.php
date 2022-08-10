<?php

namespace OAuthServer\Model\Table;

use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Table;
use Cake\Datasource\EntityInterface;
use OAuthServer\Model\Entity\AccessTokenScope;

/**
 * OAuth 2.0 access tokens to scopes table
 *
 * @property AccessTokensTable|BelongsTo $AccessTokens
 * @property ScopesTable|BelongsTo       $Scopes
 *
 * @method AccessTokenScope get($primaryKey, $options = [])
 * @method AccessTokenScope newEntity($data = null, array $options = [])
 * @method AccessTokenScope[] newEntities(array $data, array $options = [])
 * @method AccessTokenScope|bool save(EntityInterface $entity, $options = [])
 * @method AccessTokenScope patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method AccessTokenScope[] patchEntities($entities, array $data, array $options = [])
 */
class AccessTokenScopesTable extends Table
{
    /**
     * @inheritDoc
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table('oauth_access_token_scopes');
        $this->setPrimaryKey('oauth_token'); // @TODO Update after running migrations?
        $this->setEntityClass('OAuthServer.AccessTokenScope');
        $this->belongsTo('AccessTokens', [
            'className'  => 'OAuthServer.AccessTokens',
            'foreignKey' => 'oauth_token',
        ]);
        $this->belongsTo('Scopes', [
            'className' => 'OAuthServer.Scopes',
        ]);
    }
}
