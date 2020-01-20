<?php
/** @noinspection AutoloadingIssuesInspection */

use Migrations\AbstractMigration;

class DropSessions extends AbstractMigration
{
    public function up()
    {
        $this->table('oauth_session_scopes')->drop();
        $this->table('oauth_sessions')->drop();
    }

    public function down()
    {
        $table = $this->table('oauth_sessions');
        $table
            ->addColumn(
                'owner_model',
                'string',
                [
                    'limit' => 200,
                ]
            )
            ->addColumn(
                'owner_id',
                'string',
                [
                    'limit' => 20,
                ]
            )
            ->addColumn(
                'client_id',
                'string',
                [
                    'limit' => 20,
                ]
            )
            ->addColumn(
                'client_redirect_uri',
                'string',
                [
                    'default' => null,
                    'limit' => 200,
                    'null' => true,
                ]
            );
        $table->create();

        $table = $this->table('oauth_session_scopes');
        $table
            ->addColumn(
                'session_id',
                'integer',
                [
                    'length' => 11,
                ]
            )
            ->addColumn(
                'scope_id',
                'string',
                [
                    'length' => 40,
                ]
            );
        $table->create();
    }
}
