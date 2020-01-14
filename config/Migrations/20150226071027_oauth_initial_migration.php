<?php
/** @noinspection AutoloadingIssuesInspection */

use Migrations\AbstractMigration;

class OauthInitialMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * @return void
     */
    public function change()
    {
        $table = $this->table('oauth_access_tokens', ['id' => false, 'primary_key' => ['oauth_token']]);
        $table
            ->addColumn(
                'oauth_token',
                'string',
                [
                    'default' => null,
                    'limit' => 40,
                    'null' => false,
                ]
            )
            ->addColumn(
                'session_id',
                'integer',
                [
                    'default' => null,
                    'limit' => 11,
                    'null' => false,
                ]
            )
            ->addColumn(
                'expires',
                'integer',
                [
                    'default' => null,
                    'limit' => 11,
                    'null' => false,
                ]
            )
            ->create();
        $table = $this->table('oauth_auth_codes', ['id' => false, 'primary_key' => ['code']]);
        $table
            ->addColumn(
                'code',
                'string',
                [
                    'default' => null,
                    'limit' => 40,
                    'null' => false,
                ]
            )
            ->addColumn(
                'session_id',
                'integer',
                [
                    'default' => null,
                    'limit' => 11,
                    'null' => false,
                ]
            )
            ->addColumn(
                'redirect_uri',
                'string',
                [
                    'default' => null,
                    'limit' => 200,
                    'null' => false,
                ]
            )
            ->addColumn(
                'expires',
                'integer',
                [
                    'default' => null,
                    'limit' => 11,
                    'null' => false,
                ]
            )
            ->create();
        $table = $this->table('oauth_clients', ['id' => false, 'primary_key' => ['id']]);
        $table
            ->addColumn(
                'id',
                'string',
                [
                    'default' => null,
                    'limit' => 20,
                    'null' => false,
                ]
            )
            ->addColumn(
                'client_secret',
                'string',
                [
                    'default' => null,
                    'limit' => 40,
                    'null' => false,
                ]
            )
            ->addColumn(
                'name',
                'string',
                [
                    'default' => null,
                    'limit' => 200,
                    'null' => false,
                ]
            )
            ->addColumn(
                'redirect_uri',
                'string',
                [
                    'default' => null,
                    'limit' => 255,
                    'null' => false,
                ]
            )
            ->addColumn(
                'parent_model',
                'string',
                [
                    'default' => null,
                    'limit' => 200,
                    'null' => true,
                ]
            )
            ->addColumn(
                'parent_id',
                'integer',
                [
                    'default' => null,
                    'limit' => 11,
                    'null' => true,
                ]
            )
            ->create();
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
                'integer',
                [
                    'limit' => 11,
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
            )
            ->create();
        $table = $this->table('oauth_scopes', ['id' => false, 'primary_key' => ['id']]);
        $table
            ->addColumn(
                'id',
                'string',
                [
                    'default' => null,
                    'limit' => 40,
                ]
            )
            ->addColumn(
                'description',
                'string',
                [
                    'default' => null,
                    'limit' => 200,
                ]
            )
            ->create();
        $table = $this->table('oauth_refresh_tokens', ['id' => false, 'primary_key' => ['refresh_token']]);
        $table
            ->addColumn(
                'refresh_token',
                'string',
                [
                    'default' => null,
                    'limit' => 40,
                    'null' => false,
                ]
            )
            ->addColumn(
                'oauth_token',
                'string',
                [
                    'default' => null,
                    'limit' => 40,
                    'null' => false,
                ]
            )
            ->addColumn(
                'expires',
                'integer',
                [
                    'default' => null,
                    'limit' => 11,
                    'null' => false,
                ]
            )
            ->create();
        $table = $this->table('oauth_access_token_scopes');
        $table
            ->addColumn(
                'oauth_token',
                'string',
                [
                    'length' => 40,
                ]
            )
            ->addColumn(
                'scope_id',
                'string',
                [
                    'length' => 40,
                ]
            )
            ->create();
        $table = $this->table('oauth_auth_code_scopes');
        $table
            ->addColumn(
                'auth_code',
                'string',
                [
                    'length' => 40,
                ]
            )
            ->addColumn(
                'scope_id',
                'string',
                [
                    'length' => 40,
                ]
            )
            ->create();
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
            )
            ->create();
    }
}
