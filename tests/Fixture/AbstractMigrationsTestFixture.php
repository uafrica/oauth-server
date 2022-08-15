<?php

namespace OAuthServer\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;
use OAuthServer\Test\TestSuite\Lib\Factory;

/**
 * @inheritDoc
 */
abstract class AbstractMigrationsTestFixture extends TestFixture
{
    /**
     * @inheritDoc
     */
    public function init()
    {
        Factory::migrations('test_migrations')->rollback();
        Factory::migrations('test_migrations')->migrate();
        parent::init();
    }
}