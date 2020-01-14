<?php

namespace OAuthServer\Traits;

use Cake\Core\App;
use Cake\Core\Exception\Exception;

trait GetStorageTrait
{
    /**
     * Resolve a storage class name.
     *
     * @param string $class Partial class name to resolve.
     * @return string The resolved class name.
     * @throws \Cake\Core\Exception\Exception
     */
    protected function _resolveClassName($class)
    {
        $className = App::className($class, 'Model/Storage', 'Storage');
        if (!$className) {
            throw new Exception(sprintf('Storage class "%s" was not found.', $class));
        }

        return $className;
    }

    /**
     * Gets the instance of a storage class by name.
     *
     * @param string $name Storage name.
     * @return \League\OAuth2\Server\Storage\AbstractStorage
     * @throws \Cake\Core\Exception\Exception
     */
    protected function _getStorage($name)
    {
        $config = $this->getConfig('storages.' . $name);

        if (empty($config)) {
            throw new Exception(sprintf('Storage class "%s" has no configuration', $name));
        }

        $className = $this->_resolveClassName($config['className']);

        return new $className();
    }
}
