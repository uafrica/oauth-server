<?php

namespace OAuthServer\Model\Table;

use Cake\Database\Schema\Table as SchemaTable;
use Cake\ORM\Table;

/**
 * RefreshToken Model
 *
 * @property Client $Client
 * @property User $User
 */
class RefreshTokensTable extends Table
{
    /**
     * @param array $config Config
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('oauth_refresh_tokens');
        $this->primaryKey('refresh_token');
        $table = new SchemaTable(null);
        $table->addColumn('refresh_token', [
            'type' => 'string',
            'length' => 40,
            'null' => false
        ])->addColumn('oauth_token', [
            'type' => 'string',
            'length' => 40,
            'null' => false
        ])->addColumn('expires', [
            'type' => 'integer',
            'length' => 11,
            'null' => false
        ]);
        $this->schema($table);
        $this->belongsTo('AccessTokens', [
            'className' => 'OAuthServer.AccessTokens',
            'foreignKey' => 'oauth_token'
        ]);
        parent::initialize($config);
    }
}
