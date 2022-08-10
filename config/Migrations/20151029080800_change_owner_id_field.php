<?php

use Migrations\AbstractMigration;

/**
 * Sessions owner_id column table schema type migration
 */
class ChangeOwnerIdField extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up()
    {
        $this->table('oauth_sessions')
             ->changeColumn('owner_id', 'string', ['limit' => 20])
             ->update();
    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        $this->table('oauth_sessions')
             ->changeColumn('owner_id', 'int', ['limit' => 11])
             ->update();
    }
}
