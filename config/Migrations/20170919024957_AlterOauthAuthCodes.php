<?php
use Migrations\AbstractMigration;

class AlterOauthAuthCodes extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $oauthAuthCodes = $this->table('oauth_auth_codes');
        $oauthAuthCodes->changeColumn('code', 'string', array('limit' => 80));
        $oauthAuthCodes->update();
        $oauthAccessTokens = $this->table('oauth_access_tokens');
        $oauthAccessTokens->changeColumn('oauth_token', 'string', array('limit' => 128));
        $oauthAccessTokens->update();
        $oauthAccessTokens = $this->table('oauth_refresh_tokens');
        $oauthAccessTokens->changeColumn('oauth_token', 'string', array('limit' => 128));
        $oauthAccessTokens->changeColumn('refresh_token', 'string', array('limit' => 128));
        $oauthAccessTokens->update();
    }
}
