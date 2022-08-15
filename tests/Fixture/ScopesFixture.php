<?php

namespace OAuthServer\Test\Fixture;

/**
 * @inheritDoc
 */
class ScopesFixture extends AbstractMigrationsTestFixture
{
    /**
     * @inheritDoc
     */
    public $import = [
        'connection' => 'test_migrations',
        'model'      => 'OAuthServer.Scopes',
    ];

    /**
     * @inheritDoc
     */
    public $records = [
        ['id' => 'test', 'description' => 'Default scope'],
    ];
}