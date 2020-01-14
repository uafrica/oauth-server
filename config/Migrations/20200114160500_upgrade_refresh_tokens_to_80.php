<?php
/** @noinspection AutoloadingIssuesInspection */

use Migrations\AbstractMigration;

class UpgradeRefreshTokensTo80 extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('oauth_refresh_tokens');

        $table->removeColumn('session_id');

        $table->addColumn('revoked', 'boolean', [
            'default' => false,
            'null' => false,
        ]);
        $table->addTimestamps('created', 'modified');

        $table->update();
    }

    public function down()
    {
        $table = $this->table('oauth_refresh_tokens');
        $table->addColumn(
            'session_id',
            'integer',
            [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ]
        );
        $table->removeColumn('revoked');
        $table->removeColumn('created');
        $table->removeColumn('modified');

        $table->update();
    }
}
