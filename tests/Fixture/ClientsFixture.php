<?php

namespace OAuthServer\Test\Fixture;

/**
 * @inheritDoc
 */
class ClientsFixture extends AbstractMigrationsTestFixture
{
    /**
     * @inheritDoc
     */
    public $import = [
        'connection' => 'test_migrations',
        'model'      => 'OAuthServer.Clients',
    ];

    /**
     * @inheritDoc
     */
    public $records = [
        [
            'id' => 'TEST',
            'client_secret' => 'TestSecret',
            'name' => 'Test',
            'redirect_uri' => 'http://www.example.com',
        ]
    ];
}