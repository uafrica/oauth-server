<?php

use Migrations\AbstractMigration;

/**
 * Remove session related schemas since their support has been
 * dropped since thephpleague/oauth2-server:5.x
 */
class RemoveSessions extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up()
    {
        $this->table('oauth_access_tokens')
             ->removeColumn('session_id')
             ->update();

        $this->table('oauth_auth_codes')
             ->removeColumn('session_id')
             ->update();

        $this->table('oauth_sessions')
             ->drop();

        $this->table('oauth_session_scopes')
             ->drop();
    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        $this->table('oauth_access_tokens')
             ->addColumn('session_id', 'integer', ['default' => null, 'limit' => 11, 'null' => false, 'after' => 'oauth_token'])
             ->update();

        $this->table('oauth_auth_codes', ['id' => false, 'primary_key' => ['code']])
             ->addColumn('session_id', 'integer', ['default' => null, 'limit' => 11, 'null' => false, 'after' => 'code'])
             ->update();

        $this->table('oauth_sessions')
             ->addColumn('owner_model', 'string', ['limit' => 200])
             ->addColumn('owner_id', 'integer', ['limit' => 11])
             ->addColumn('client_id', 'string', ['limit' => 20])
             ->addColumn('client_redirect_uri', 'string', ['default' => null, 'limit' => 200, 'null' => true])
             ->create();

        $this->table('oauth_session_scopes')
             ->addColumn('session_id', 'integer', ['length' => 11])
             ->addColumn('scope_id', 'string', ['length' => 40])
             ->create();
    }
}
