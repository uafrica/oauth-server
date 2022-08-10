<?php

use Migrations\AbstractMigration;

/**
 * Add support for storing client and user identifiers
 * with the access and authentication tokens
 * since thephpleague/oauth2-server:5.x
 */
class AddTokenClientAndUserAssoc extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up()
    {
        $this->table('oauth_access_tokens')
             ->addColumn('client_id', 'string', ['default' => null, 'limit' => 20, 'null' => false])
             ->addColumn('user_id', 'string', ['default' => null, 'limit' => 36, 'null' => true])
             ->update();

        $this->table('oauth_auth_codes')
             ->addColumn('client_id', 'string', ['default' => null, 'limit' => 20, 'null' => false])
             ->addColumn('user_id', 'string', ['default' => null, 'limit' => 36, 'null' => true])
             ->update();
    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        $this->table('oauth_access_tokens')
             ->removeColumn('client_id')
             ->removeColumn('user_id')
             ->update();

        $this->table('oauth_auth_codes')
             ->removeColumn('client_id')
             ->removeColumn('user_id')
             ->update();
    }
}
