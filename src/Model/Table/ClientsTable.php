<?php

namespace OAuthServer\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Table;
use Cake\Datasource\EntityInterface;
use OAuthServer\Lib\Factory;
use OAuthServer\Model\Entity\Client;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

/**
 * OAuth 2.0 clients table
 *
 * @method Client get($primaryKey, $options = [])
 * @method Client newEntity($data = null, array $options = [])
 * @method Client[] newEntities(array $data, array $options = [])
 * @method Client|bool save(EntityInterface $entity, $options = [])
 * @method Client patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method Client[] patchEntities($entities, array $data, array $options = [])
 */
class ClientsTable extends Table implements ClientRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table('oauth_clients');
        $this->setEntityClass('OAuthServer.Client');
        $this->primaryKey('id'); // @TODO Update after running migrations?
        $this->displayField('name');
    }

    /**
     * @param Event  $event  Event object
     * @param Client $client Client entity
     * @return void
     */
    public function beforeSave(Event $event, Client $client)
    {
        if ($client->isNew()) {
            $client->id            = Factory::clientId();
            $client->client_secret = Factory::clientSecret();
        }
    }

    /**
     * @inheritDoc
     */
    public function getClientEntity($clientIdentifier)
    {
        if ($client = $this->find()->where([$this->getPrimaryKey() => $clientIdentifier])->first()) {
            return $client->transformToDTO();
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType)
    {
        $event = new Event('OAuthServer.validateClient', $this, [$clientIdentifier, $clientSecret, $grantType]);
        $this->getEventManager()->dispatch($event);
        if ($event->isStopped()) {
            return false;
        }
        /** @var Client $entity */
        if (!$entity = $this->find()->where([$this->getPrimaryKey() => $clientIdentifier])->first()) {
            return false;
        }
        if ($entity->client_secret !== $clientSecret) {
            return false;
        }
        return true;
    }
}
