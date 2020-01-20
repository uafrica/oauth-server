<?php
/** @noinspection AutoloadingIssuesInspection */

use Migrations\AbstractMigration;

class DropSessions extends AbstractMigration
{
    public function change()
    {
        $this->table('oauth_scopes')->drop();
        $this->table('oauth_sessions')->drop();
    }
}
