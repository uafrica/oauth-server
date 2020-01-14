<?php

namespace OAuthServer\Test\TestCase\Model\Entity;

use Cake\TestSuite\TestCase;
use OAuthServer\Model\Entity\Client;

class ClientTest extends TestCase
{
    /**
     * @var Client
     */
    private $subject;

    public function setUp()
    {
        parent::setUp();
        $this->subject = new Client([
            'name' => 'Test Client',
            'redirect_uri' => ['https://example.com'],
        ]);

        $this->subject->id = 'TEST';
        $this->subject->client_secret = 'TestSecretToken';
    }

    public function tearDown()
    {
        unset($this->subject);
        parent::tearDown();
    }

    public function testIsConfidential()
    {
        $this->assertTrue($this->subject->isConfidential());
        //
        $this->subject->client_secret = null;
        $this->assertFalse($this->subject->isConfidential());
    }

    public function testGetIdentifier()
    {
        $this->assertSame('TEST', $this->subject->getIdentifier());
    }

    public function testGetRedirectUri()
    {
        $this->assertSame(['https://example.com'], $this->subject->getRedirectUri());
    }

    public function testGetName()
    {
        $this->assertSame('Test Client', $this->subject->getName());
    }
}
