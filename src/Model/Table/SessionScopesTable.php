<?php
namespace OAuthServer\Model\Table;

use Cake\Database\Schema\Table as SchemaTable;
use Cake\ORM\Table;

class SessionScopesTable extends Table
{
    /**
     * @param array $config Config
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('oauth_session_scopes');
        $table = new SchemaTable(null);
        $table->addColumn('id', [
            'type' => 'integer',
            'length' => 11,
            'null' => false
        ])->addColumn('session_id', [
            'type' => 'integer',
            'length' => 11,
            'null' => false
        ])->addColumn('scope_id', [
            'type' => 'string',
            'length' => 40,
            'null' => false
        ]);
        $this->schema($table);
        $this->belongsTo('Sessions', [
                'className' => 'OAuthServer.Sessions',
            ]);
        $this->belongsTo('Scopes', [
                'className' => 'OAuthServer.Scopes'
            ]);
        parent::initialize($config);
    }
}
