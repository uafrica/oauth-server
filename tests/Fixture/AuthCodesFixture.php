<?php

namespace OAuthServer\Test\Fixture;

/**
 * @inheritDoc
 */
class AuthCodesFixture extends AbstractMigrationsTestFixture
{
    /**
     * @inheritDoc
     */
    public $import = [
        'connection' => 'test_migrations',
        'model'      => 'OAuthServer.AuthCodes',
    ];
}