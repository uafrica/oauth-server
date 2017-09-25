<?php
namespace OAuthServer\Model\Storage;

use Cake\Datasource\ModelAwareTrait;
use League\OAuth2\Server\Storage\AbstractStorage as BaseAbstractStorage;

abstract class AbstractStorage extends BaseAbstractStorage
{
    use ModelAwareTrait;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->modelFactory('Table', ['Cake\ORM\TableRegistry', 'get']);
    }
}
