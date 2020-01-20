<?php
/** @noinspection AutoloadingIssuesInspection */

use Migrations\AbstractMigration;

class UpgradeAccessTokensTo80 extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('oauth_access_tokens');

        $table->removeColumn('session_id');

        $table->changeColumn('oauth_token', 'string', [
            'default' => null,
            'limit' => 80,
            'null' => false,
        ]);

        $table->addColumn('client_id', 'string', [
            'default' => null,
            'limit' => 20,
            'null' => false,
        ]);
        $table->addColumn('user_id', 'string', [
            'default' => null,
            'limit' => 36,
            'null' => true,
        ]);
        $table->addColumn('revoked', 'boolean', [
            'default' => false,
            'null' => false,
        ]);
        $table->addTimestamps('created', 'modified');

        $table->update();
    }

    public function down()
    {
        $table = $this->table('oauth_access_tokens');
        $table->addColumn(
            'session_id',
            'integer',
            [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ]
        );
        $table->removeColumn('client_id');
        $table->removeColumn('user_id');
        $table->removeColumn('revoked');
        $table->removeColumn('created');
        $table->removeColumn('modified');

        $table->update();
    }
}
