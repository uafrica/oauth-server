<?php

use Migrations\AbstractMigration;

/**
 * Correct identifier length from 40 to 80 due to
 * bin2hex'ing 40 random bytes by default result in 80 chars
 */
class CorrectIdentifierLength extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up()
    {
        $this->table('oauth_access_tokens')
             ->changeColumn('oauth_token', 'string', ['limit' => 80])
             ->update();

        $this->table('oauth_auth_codes')
             ->changeColumn('code', 'string', ['limit' => 80])
             ->update();

        $this->table('oauth_scopes', ['id' => false, 'primary_key' => ['id']])
             ->changeColumn('id', 'string', ['limit' => 80])
             ->update();

        $this->table('oauth_refresh_tokens')
             ->changeColumn('refresh_token', 'string', ['limit' => 80])
             ->changeColumn('oauth_token', 'string', ['limit' => 80])
             ->update();

        $this->table('oauth_access_token_scopes')
             ->changeColumn('oauth_token', 'string', ['length' => 80])
             ->changeColumn('scope_id', 'string', ['length' => 80])
             ->update();

        $this->table('oauth_auth_code_scopes')
             ->changeColumn('auth_code', 'string', ['length' => 80])
             ->changeColumn('scope_id', 'string', ['length' => 80])
             ->update();

        $this->table('oauth_session_scopes')
             ->changeColumn('scope_id', 'string', ['length' => 80])
             ->update();
    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        $this->table('oauth_access_tokens')
             ->changeColumn('oauth_token', 'string', ['limit' => 40])
             ->update();

        $this->table('oauth_auth_codes')
             ->changeColumn('code', 'string', ['limit' => 40])
             ->update();

        $this->table('oauth_scopes', ['id' => false, 'primary_key' => ['id']])
             ->changeColumn('id', 'string', ['limit' => 40])
             ->update();

        $this->table('oauth_refresh_tokens')
             ->changeColumn('refresh_token', 'string', ['limit' => 40])
             ->changeColumn('oauth_token', 'string', ['limit' => 40])
             ->update();

        $this->table('oauth_access_token_scopes')
             ->changeColumn('oauth_token', 'string', ['length' => 40])
             ->changeColumn('scope_id', 'string', ['length' => 40])
             ->update();

        $this->table('oauth_auth_code_scopes')
             ->changeColumn('auth_code', 'string', ['length' => 40])
             ->changeColumn('scope_id', 'string', ['length' => 40])
             ->update();

        $this->table('oauth_session_scopes')
             ->changeColumn('scope_id', 'string', ['length' => 40])
             ->update();
    }
}
