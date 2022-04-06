<?php

namespace OAuthServer\Model\Table;

use Cake\ORM\Table;

/**
 * Auth Code Scope Model
 */
class OauthAuthCodeScopesTable extends Table
{
    /**
     * {@inheritDoc}
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('oauth_auth_code_scopes');
        $this->setPrimaryKey('id');
    }
}
