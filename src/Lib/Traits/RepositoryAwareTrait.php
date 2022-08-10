<?php

namespace OAuthServer\Lib\Traits;

use Cake\Datasource\RepositoryInterface;
use OAuthServer\Lib\Enum\Repository;
use OAuthServer\Plugin;

/**
 * Helper trait that sets the repository on the calling object
 */
trait RepositoryAwareTrait
{
    /**
     * Loads the Cake repository/table object that corresponds with the
     * configured enumerated repository value (e.g. Repository::ACCESS_TOKEN)
     * and sets it on a property called $name on the calling object
     *
     * @param string     $name       Used to set it on the object
     * @param Repository $repository Value from repository enumeration
     * @return RepositoryInterface
     */
    public function loadRepository(string $name, Repository $repository): RepositoryInterface
    {
        $repository = Plugin::instance()->getRepository($repository);
        return $this->{$name} = $repository;
    }
}
