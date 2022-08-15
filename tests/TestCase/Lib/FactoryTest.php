<?php

namespace OAuthServer\Test\TestCase\Controller;

use Cake\TestSuite\TestCase;
use OAuthServer\Lib\Enum\Repository;
use OAuthServer\Lib\Factory;
use DateInterval;
use InvalidArgumentException;
use stdClass;
use OAuthServer\Lib\Enum\Token;

class FactoryTest extends TestCase
{
    /**
     * @return void
     */
    public function testClientId(): void
    {
        $clientId = Factory::clientId();
        $this->assertInternalType('string', $clientId);
        $this->assertEquals(20, strlen($clientId));
    }

    /**
     * @return void
     */
    public function testClientSecret(): void
    {
        $clientSecret = Factory::clientSecret();
        $this->assertInternalType('string', $clientSecret);
        $this->assertEquals(40, strlen($clientSecret));
    }

    /**
     * @return void
     */
    public function testDateInterval(): void
    {
        $dateInterval = Factory::dateInterval('P1M');
        $this->assertInstanceOf(DateInterval::class, $dateInterval);
        $dateInterval = Factory::dateInterval($dateInterval);
        $this->assertInstanceOf(DateInterval::class, $dateInterval);
        $this->expectException(InvalidArgumentException::class);
        Factory::dateInterval(new stdClass());
        $this->expectException(InvalidArgumentException::class);
        Factory::dateInterval('NOTADURATIONSTRING');
    }

    /**
     * @return void
     */
    public function testTimeToLiveIntervals(): void
    {
        $intervals = Factory::timeToLiveIntervals([Token::ACCESS_TOKEN => 'P1M']);
        $this->assertInternalType('array', $intervals);
        $this->assertArrayHasKey(Token::ACCESS_TOKEN, $intervals);
        $this->assertInstanceOf(DateInterval::class, $intervals[Token::ACCESS_TOKEN]);
    }

    /**
     * @return void
     */
    public function testIntervalTimestamp(): void
    {
        $timestamp = Factory::intervalTimestamp(new DateInterval('PT1S'));
        $this->assertEquals(1, $timestamp);
    }

    /**
     * @return void
     */
    public function testRepositories(): void
    {
        $repositories = Factory::repositories([]);
        $this->assertInternalType('array', $repositories);
        foreach (Repository::toArray() as $className) {
            $this->assertArrayHasKey($className, $repositories);
            $this->assertInstanceOf($className, $repositories[$className]);
        }
    }
}