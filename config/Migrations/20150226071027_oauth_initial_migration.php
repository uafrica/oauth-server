<?php

use Migrations\AbstractMigration;

/**
 * Initial migration of OAuth 2.0 table schemas
 */
class OauthInitialMigration extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->table('oauth_access_tokens', ['id' => false, 'primary_key' => ['oauth_token']])
             ->addColumn('oauth_token', 'string', ['default' => null, 'limit' => 40, 'null' => false])
             ->addColumn('session_id', 'integer', ['default' => null, 'limit' => 11, 'null' => false])
             ->addColumn('expires', 'integer', ['default' => null, 'limit' => 11, 'null' => false])
             ->create();

        $this->table('oauth_auth_codes', ['id' => false, 'primary_key' => ['code']])
             ->addColumn('code', 'string', ['default' => null, 'limit' => 40, 'null' => false])
             ->addColumn('session_id', 'integer', ['default' => null, 'limit' => 11, 'null' => false])
             ->addColumn('redirect_uri', 'string', ['default' => null, 'limit' => 200, 'null' => false])
             ->addColumn('expires', 'integer', ['default' => null, 'limit' => 11, 'null' => false])
             ->create();

        $this->table('oauth_clients', ['id' => false, 'primary_key' => ['id']])
             ->addColumn('id', 'string', ['default' => null, 'limit' => 20, 'null' => false])
             ->addColumn('client_secret', 'string', ['default' => null, 'limit' => 40, 'null' => false])
             ->addColumn('name', 'string', ['default' => null, 'limit' => 200, 'null' => false])
             ->addColumn('redirect_uri', 'string', ['default' => null, 'limit' => 255, 'null' => false])
             ->addColumn('parent_model', 'string', ['default' => null, 'limit' => 200, 'null' => true])
             ->addColumn('parent_id', 'integer', ['default' => null, 'limit' => 11, 'null' => true])
             ->create();

        $this->table('oauth_sessions')
             ->addColumn('owner_model', 'string', ['limit' => 200])
             ->addColumn('owner_id', 'integer', ['limit' => 11])
             ->addColumn('client_id', 'string', ['limit' => 20])
             ->addColumn('client_redirect_uri', 'string', ['default' => null, 'limit' => 200, 'null' => true])
             ->create();

        $this->table('oauth_scopes', ['id' => false, 'primary_key' => ['id']])
             ->addColumn('id', 'string', ['default' => null, 'limit' => 40])
             ->addColumn('description', 'string', ['default' => null, 'limit' => 200])
             ->create();

        $this->table('oauth_refresh_tokens', ['id' => false, 'primary_key' => ['refresh_token']])
             ->addColumn('refresh_token', 'string', ['default' => null, 'limit' => 40, 'null' => false])
             ->addColumn('oauth_token', 'string', ['default' => null, 'limit' => 40, 'null' => false])
             ->addColumn('expires', 'integer', ['default' => null, 'limit' => 11, 'null' => false])
             ->create();

        $this->table('oauth_access_token_scopes')
             ->addColumn('oauth_token', 'string', ['length' => 40])
             ->addColumn('scope_id', 'string', ['length' => 40])
             ->create();

        $this->table('oauth_auth_code_scopes')
             ->addColumn('auth_code', 'string', ['length' => 40])
             ->addColumn('scope_id', 'string', ['length' => 40])
             ->create();

        $this->table('oauth_session_scopes')
             ->addColumn('session_id', 'integer', ['length' => 11])
             ->addColumn('scope_id', 'string', ['length' => 40])
             ->create();
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('oauth_access_tokens');
        $this->dropTable('oauth_auth_codes');
        $this->dropTable('oauth_clients');
        $this->dropTable('oauth_sessions');
        $this->dropTable('oauth_scopes');
        $this->dropTable('oauth_refresh_tokens');
        $this->dropTable('oauth_access_token_scopes');
        $this->dropTable('oauth_auth_code_scopes');
        $this->dropTable('oauth_session_scopes');
    }
}
