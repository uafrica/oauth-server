<?php

namespace OAuthServer\Test\TestCase\Model\Entity;

use Cake\TestSuite\TestCase;
use OAuthServer\Model\Entity\Scope;

class ScopeTest extends TestCase
{
    public function testGetIdentifier()
    {
        $entity = new Scope(['id' => 'email']);

        $this->assertSame('email', $entity->getIdentifier());
    }
}
