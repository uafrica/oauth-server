<?php

namespace OAuthServer\Model\Table;

use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Table;
use OAuthServer\Model\Entity\AuthCodeScope;
use Cake\Datasource\EntityInterface;

/**
 * OAuth 2.0 authorisation codes to scopes table
 *
 * @property AuthCodesTable|BelongsTo $AuthCodes
 * @property ScopesTable|BelongsTo    $Scopes
 *
 * @method AuthCodeScope get($primaryKey, $options = [])
 * @method AuthCodeScope newEntity($data = null, array $options = [])
 * @method AuthCodeScope[] newEntities(array $data, array $options = [])
 * @method AuthCodeScope|bool save(EntityInterface $entity, $options = [])
 * @method AuthCodeScope patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method AuthCodeScope[] patchEntities($entities, array $data, array $options = [])
 */
class AuthCodeScopesTable extends Table
{
    /**
     * @inheritDoc
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table('oauth_auth_code_scopes');
        $this->setPrimaryKey('auth_code'); // @TODO Update after running migrations?
        $this->setEntityClass('OAuthServer.AuthCodeScope');
        $this->belongsTo('AuthCodes', [
            'className'    => 'OAuthServer.AuthCodes',
            'foreignKey'   => 'auth_code',
            'propertyName' => 'code',
        ]);
        $this->belongsTo('Scopes', [
            'className' => 'OAuthServer.Scopes',
        ]);
    }
}
