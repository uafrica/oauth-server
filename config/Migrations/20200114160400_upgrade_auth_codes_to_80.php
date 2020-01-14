<?php
/** @noinspection AutoloadingIssuesInspection */

use Migrations\AbstractMigration;

class UpgradeAuthCodesTo80 extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('oauth_auth_codes');

        $table->removeColumn('session_id');

        $table->addColumn('client_id', 'string', [
            'default' => null,
            'limit' => 20,
            'null' => false,
        ]);
        $table->addColumn('user_id', 'string', [
            'default' => null,
            'limit' => 36,
            'null' => false,
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
        $table = $this->table('oauth_auth_codes');
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
