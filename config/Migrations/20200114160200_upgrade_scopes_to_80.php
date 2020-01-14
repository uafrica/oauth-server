<?php
/** @noinspection AutoloadingIssuesInspection */

use Migrations\AbstractMigration;

class UpgradeScopesTo80 extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('oauth_scopes');
        $table->addTimestamps('created', 'modified');

        $table->update();
    }

    public function down()
    {
        $table = $this->table('oauth_scopes');
        $table->removeColumn('created');
        $table->removeColumn('modified');

        $table->update();
    }
}
