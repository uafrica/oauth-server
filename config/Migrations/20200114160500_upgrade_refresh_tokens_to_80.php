<?php
/** @noinspection AutoloadingIssuesInspection */

use Migrations\AbstractMigration;

class UpgradeRefreshTokensTo80 extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('oauth_refresh_tokens');

        $table->removeColumn('session_id');

        $table->changeColumn('refresh_token', 'string', [
            'default' => null,
            'limit' => 80,
            'null' => false,
        ]);

        $table->changeColumn('oauth_token', 'string', [
            'default' => null,
            'limit' => 80,
            'null' => false,
        ]);

        $table->addColumn('revoked', 'boolean', [
            'default' => false,
            'null' => false,
        ]);
        $table->addTimestamps('created', 'modified');

        $table->update();
    }
}
