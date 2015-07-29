<?php

namespace OAuthServer\Model\Table;

use Cake\ORM\Table;
use Cake\Database\Schema\Table as SchemaTable;

/**
 * AuthCode Model
 *
 * @property Client $Client
 * @property User $User
 */
class AuthCodesTable extends Table
{
    /**
     * @param array $config Config
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('oauth_auth_codes');
        $this->primaryKey('code');
		$table = new SchemaTable(null);
		$table
			->addColumn('code', [
				'type' => 'string',
				'length' => 40,
				'null' => false
			])->addColumn('session_id', [
				'type' => 'integer',
				'length' => 11,
				'null' => false
			])->addColumn('redirect_uri', [
				'type' => 'string',
				'length' => 200,
				'null' => false
			])->addColumn('expires', [
				'type' => 'integer',
				'length' => 11,
				'null' => false
			]);
		$this->schema($table);
        $this->belongsTo(
            'Sessions',
            [
                'className' => 'OAuthServer.Sessions',
                'foreignKey' => 'session_id'
            ]
        );
        $this->hasMany(
            'AuthCodeScopes',
            [
                'className' => 'OAuthServer.AuthCodeScopes',
                'foreignKey' => 'auth_code',
                'dependant' => true
            ]
        );
        parent::initialize($config);
    }
}
