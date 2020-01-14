<?php
/** @noinspection AutoloadingIssuesInspection */

use Migrations\AbstractMigration;

class UpgradeClientsTo80 extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('oauth_clients');
        $table
            ->changeColumn('redirect_uri', 'text');
        $table->addColumn('grant_types', 'text', [
            'default' => null,
            'null' => true,
        ]);
        $table->addTimestamps('created', 'modified');

        $table->update();
    }

    public function down()
    {
        $table = $this->table('oauth_clients');
        $table->changeColumn('redirect_uri', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->removeColumn('grant_types');
        $table->removeColumn('created');
        $table->removeColumn('modified');

        $table->update();
    }
}
