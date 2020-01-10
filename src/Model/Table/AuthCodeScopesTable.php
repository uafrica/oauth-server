<?php
namespace OAuthServer\Model\Table;

use Cake\ORM\Table;

class AuthCodeScopesTable extends Table
{
    /**
     * @param array $config Config
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('oauth_auth_code_scopes');
        $this->belongsTo('AuthCodes', [
                'className' => 'OAuthServer.AuthCodes',
                'foreignKey' => 'auth_code',
                'propertyName' => 'code',
            ]);
        $this->belongsTo('Scopes', [
                'className' => 'OAuthServer.Scopes',
            ]);
        parent::initialize($config);
    }
}
