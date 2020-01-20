<?php
/** @noinspection AutoloadingIssuesInspection */

use Cake\Datasource\EntityInterface;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
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

        $clients = TableRegistry::getTableLocator()->get('OauthClients');
        $clients->find()->all()
            ->each(static function (EntityInterface $entity) use ($clients) {
                if (is_string($entity->redirect_uri)) {
                    $entity->redirect_uri = json_encode([$entity->redirect_uri]);
                    $clients->save($entity);
                }
            });
    }

    public function down()
    {
        $clients = TableRegistry::getTableLocator()->get('OauthClients');
        $clients->find()->all()
            ->each(static function (EntityInterface $entity) use ($clients) {
                $redirectUrl = $entity->redirect_uri;
                if (preg_match('/\A\[.+\]\z/', $redirectUrl)) {
                    $entity->redirect_uri = json_decode($redirectUrl, true)[0];
                    $clients->save($entity);
                }
            });

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
