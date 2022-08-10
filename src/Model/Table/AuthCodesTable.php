<?php

namespace OAuthServer\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Table;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use OAuthServer\Model\Entity\AuthCode;
use OAuthServer\Lib\Data\Entity\AuthCode as AuthCodeData;

/**
 * OAuth 2.0 authorisation codes table
 *
 * @property AuthCodeScopesTable|HasMany $AuthCodeScopes
 *
 * @method AuthCode get($primaryKey, $options = [])
 * @method AuthCode newEntity($data = null, array $options = [])
 * @method AuthCode[] newEntities(array $data, array $options = [])
 * @method AuthCode|bool save(EntityInterface $entity, $options = [])
 * @method AuthCode patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method AuthCode[] patchEntities($entities, array $data, array $options = [])
 */
class AuthCodesTable extends Table implements AuthCodeRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table('oauth_auth_codes');
        $this->setEntityClass('OAuthServer.AuthCode');
        $this->primaryKey('code'); // @TODO Update after running migrations?
        $this->hasMany('AuthCodeScopes', [
            'className'  => 'OAuthServer.AuthCodeScopes',
            'foreignKey' => 'auth_code',
            'dependant'  => true,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getNewAuthCode()
    {
        return new AuthCodeData();
    }

    /**
     * @inheritDoc
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        $entity = $this->newEntity([
            'code'             => $authCodeEntity->getIdentifier(),
            'expires'          => $authCodeEntity->getExpiryDateTime()->getTimestamp(),
            'client_id'        => $accessTokenEntity->getClient()->getIdentifier(),
            'user_id'          => $accessTokenEntity->getUserIdentifier(),
            'redirect_uri'     => $authCodeEntity->getRedirectUri(),
            'auth_code_scopes' => map($accessTokenEntity->getScopes(), fn(ScopeEntityInterface $scope) => [
                'oauth_token' => $accessTokenEntity->getIdentifier(),
                'scope_id'    => $scope->getIdentifier(),
            ]),
        ]);
        $this->saveOrFail($entity);
    }

    /**
     * @inheritDoc
     */
    public function revokeAuthCode($codeId)
    {
        if ($entity = $this->get($codeId)) {
            $this->delete($entity);
        }
    }

    /**
     * @inheritDoc
     */
    public function isAuthCodeRevoked($codeId)
    {
        return !$this
            ->find()
            ->where([$this->getPrimaryKey() => $codeId])
            ->count();
    }
}
