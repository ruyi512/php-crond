<?php

namespace Wangruyi\PhpCrond\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wangruyi\PhpCrond\Scheduler;
use Wangruyi\PhpCrond\Job;
use Psr\Log\LoggerInterface;

class SchedulerTest extends TestCase
{
    public function testConstructorWithName()
    {
        $scheduler = new Scheduler('my-scheduler');
        $this->assertEquals('my-scheduler', $scheduler->getName());
    }

    public function testConstructorDefaultName()
    {
        $scheduler = new Scheduler();
        $this->assertEquals('scheduler', $scheduler->getName());
    }

    public function testAddJobValidMoment()
    {
        $scheduler = new Scheduler();
        $job = new Job('* * * * *', 'php -v');
        $result = $scheduler->addJob($job);
        $this->assertEquals(1, $result);
    }

    public function testAddJobInvalidMomentThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $scheduler = new Scheduler();
        $job = new Job('invalid', 'php -v');
        $scheduler->addJob($job);
    }

    public function testAddJobs()
    {
        $scheduler = new Scheduler();
        $jobs = [
            new Job('* * * * *', 'php -v'),
            new Job('0 * * * *', 'php -i'),
        ];
        $scheduler->addJobs($jobs);
        // No exception, we can't assert internal jobs array directly
        $this->assertTrue(true);
    }

    public function testSetLogger()
    {
        $scheduler = new Scheduler();
        $logger = $this->createMock(LoggerInterface::class);
        $scheduler->setLogger($logger);
        $this->assertSame($logger, $scheduler->getLogger());
    }

    public function testGetLoggerCreatesDefaultLogger()
    {
        $scheduler = new Scheduler();
        $logger = $scheduler->getLogger();
        $this->assertInstanceOf(\Monolog\Logger::class, $logger);
        $this->assertEquals('scheduler', $logger->getName());
    }

    public function testGetLoggerWithCustomName()
    {
        $scheduler = new Scheduler('custom');
        $logger = $scheduler->getLogger();
        $this->assertEquals('custom', $logger->getName());
    }

    // We cannot test run() easily because it's an infinite loop.
    // For now, skip.
}