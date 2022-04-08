<?php

namespace OAuthServer\Model\Table;

use Cake\Database\Schema\TableSchema;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Table;
use Cake\Utility\Security;
use Cake\Utility\Text;
use Cake\Validation\Validation;
use Cake\Validation\Validator;
use OAuthServer\Bridge\GrantTypes;
use OAuthServer\Model\Entity\Client;

/**
 * Client Model
 *
 * @method Client get($primaryKey, $options = [])
 * @method Client newEntity($data = null, array $options = [])
 * @method Client[] newEntities(array $data, array $options = [])
 * @method Client|bool save(EntityInterface $entity, $options = [])
 * @method Client patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method Client[] patchEntities($entities, array $data, array $options = [])
 * @method Client findOrCreate($search, callable $callback = null, $options = [])
 */
class OauthClientsTable extends Table
{
    /**
     * {@inheritDoc}
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('oauth_clients');
        $this->setEntityClass(Client::class);
        $this->setPrimaryKey('id');
        $this->setDisplayField('name');
    }

    /**
     * {@inheritDoc}
     */
    protected function _initializeSchema(TableSchema $schema): TableSchema
    {
        $schema->setColumnType('redirect_uri', 'json');
        $schema->setColumnType('grant_types', 'json');

        return $schema;
    }

    /**
     * {@inheritDoc}
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator->maxLength('id', 20);
        $validator->maxLength('client_secret', 40);
        $validator
            ->requirePresence('name', 'create')
            ->maxLength('name', 200)
            ->notEmpty('name');

        $validator
            ->requirePresence('redirect_uri', 'create')
            ->isArray('redirect_uri')
            ->add('redirect_uri', 'url', [
                'rule' => static function ($check) {
                    $check = (array)$check;
                    foreach ($check as $uri) {
                        if (preg_match('!\Ahttps?://!', $uri) && !Validation::url($uri, true)) {
                            return false;
                        }
                        if (!preg_match('!\A(?:[A-z][A-z0-9+-.]+)://!', $uri)) {
                            // not have url scheme
                            return false;
                        }
                    }

                    return true;
                },
                'message' => __('the redirect_uri contains invalid uri.'),
            ]);

        $validator
            ->isArray('grant_types')
            ->allowEmpty('grant_types')
            ->add('grant_types', 'allowed', [
                'rule' => static function ($check) {
                    $check = (array)$check;
                    $validGrantTypes = GrantTypes::getAllowedGrantTypes();
                    foreach ($check as $grantType) {
                        if (!in_array($grantType, $validGrantTypes, true)) {
                            return false;
                        }
                    }

                    return true;
                },
                'message' => __('the grant_types contains invalid grant type.'),
            ]);

        return $validator;
    }

    /**
     * @param Event $event Event object
     * @param Client $client Client entity
     * @return void
     * @noinspection PhpUnusedParameterInspection
     */
    public function beforeSave(Event $event, Client $client): void
    {
        if ($client->isNew()) {
            $client->id = $client->id ?? $this->generateClientId();
            $client->client_secret = $this->generateSecret();
        }
    }

    /**
     * Generate client id
     *
     * @return string e.g. NGYcZDRjODcxYzFkY2Rk (seems popular format)
     */
    public function generateClientId(): string
    {
        return base64_encode(uniqid('', false) . substr(uniqid('', true), 11, 2));
    }

    /**
     * Generate client secret
     *
     * @return string
     */
    public function generateSecret(): string
    {
        return Security::hash(Text::uuid(), 'sha1', true);
    }
}
