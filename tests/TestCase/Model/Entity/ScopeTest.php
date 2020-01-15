<?php

namespace OAuthServer\Test\TestCase\Model\Entity;

use OAuthServer\Model\Entity\Scope;
use PHPUnit\Framework\TestCase;

class ScopeTest extends TestCase
{
    public function testJsonSerialize()
    {
        $this->assertSame('"test"', json_encode(new Scope(['id' => 'test'])));
    }
}
