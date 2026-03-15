<?php

namespace Wangruyi\PhpCrond\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wangruyi\PhpCrond\Worker;
use Wangruyi\PhpCrond\Job;

class WorkerTest extends TestCase
{
    public function testRunSuccess()
    {
        $job = new Job('* * * * *', 'php -r "echo 1;"');
        // Should not throw exception
        $this->expectNotToPerformAssertions();
        Worker::run($job);
    }

    public function testRunFailureThrowsException()
    {
        $this->expectException(\RuntimeException::class);
        // Use a not exists dir
        $job = new Job('* * * * *', 'php -r "exit(1);"', '', '', '/dir');
        Worker::run($job);
    }

    public function testRunWithCwd()
    {
        $cwd = sys_get_temp_dir();
        $job = new Job('* * * * *', 'php -r "echo getcwd();"', '', '', $cwd);
        // Should not throw
        $this->expectNotToPerformAssertions();
        Worker::run($job);
    }
}