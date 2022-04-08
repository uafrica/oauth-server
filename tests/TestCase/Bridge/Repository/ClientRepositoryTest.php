<?php

namespace OAuthServer\Test\TestCase\Bridge\Repository;

use Cake\TestSuite\TestCase;
use OAuthServer\Bridge\Repository\ClientRepository;

class ClientRepositoryTest extends TestCase
{
    public $fixtures = [
        'plugin.OAuthServer.Clients',
    ];

    /**
     * @var ClientRepository
     */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->repository = new ClientRepository();
    }

    public function tearDown()
    {
        unset($this->repository);
        parent::tearDown();
    }

    public function testGetClientEntity()
    {
        $entity = $this->repository->getClientEntity('TEST');

        $this->assertSame('TEST', $entity->getIdentifier());
    }

    /**
     * @dataProvider dataValidateClient
     * @param array $inputs
     * @param bool $expects
     */
    public function testValidateClient($inputs, $expects)
    {
        [$clientIdentifier, $clientSecret, $grantType] = $inputs;

        $this->assertSame($expects, $this->repository->validateClient($clientIdentifier, $clientSecret, $grantType));
    }

    public function dataValidateClient()
    {
        return [
            'valid: Client id only' => [
                ['TEST', null, null],
                true,
            ],
            'valid: Client id with secret' => [
                ['TEST', 'TestSecret', null],
                true,
            ],
            'invalid: Client id only' => [
                ['INVALID', null, null],
                false,
            ],
            'invalid: Client id with secret' => [
                ['TEST', 'invalid', null],
                false,
            ],
            'valid: with grant type' => [
                ['AuthCodeOnly', 'TestSecret', 'authorization_code'],
                true,
            ],
            'invalid: with grant type' => [
                ['AuthCodeOnly', 'TestSecret', 'password'],
                false,
            ],
        ];
    }
}
