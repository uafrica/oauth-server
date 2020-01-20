<?php
/** @noinspection AutoloadingIssuesInspection */

use Migrations\AbstractMigration;

class UpgradeScopesTo80 extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('oauth_scopes');
        $table->addTimestamps('created', 'modified');

        $table->update();
    }
}
