<?php

namespace Wangruyi\PhpCrond\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wangruyi\PhpCrond\Job;
use Wangruyi\PhpCrond\FileOutput;

class JobTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $job = new Job('* * * * *', 'php -v', 'test job', '/tmp/output.log', '/tmp');
        $this->assertEquals('* * * * *', $job->getMoment());
        $this->assertEquals('php -v', $job->getCommand());
        $this->assertEquals('test job', $job->getName());
        $this->assertEquals('/tmp', $job->getCwd());
        // getOutput returns file path after processing
        $this->assertEquals('/tmp/output.log', $job->getOutput());
    }

    public function testConstructorDefaults()
    {
        $job = new Job('* * * * *', 'php -v');
        $this->assertEquals('', $job->getName());
        $this->assertEquals(getcwd(), $job->getCwd());
        // Output is empty string, getOutput will return empty string? Actually output is empty string, setOutput will create FileOutput with empty string? Let's see: setOutput('') creates new FileOutput(''), getFilePath returns ''.
        $this->assertEquals('', $job->getOutput());
    }

    public function testSetOutputWithString()
    {
        $job = new Job('* * * * *', 'php -v');
        $job->setOutput('/var/log/app.log');
        $this->assertEquals('/var/log/app.log', $job->getOutput());
    }

    public function testSetOutputWithFileOutputInstance()
    {
        $fileOutput = new FileOutput('/var/log/app.log');
        $job = new Job('* * * * *', 'php -v');
        $job->setOutput($fileOutput);
        $this->assertEquals('/var/log/app.log', $job->getOutput());
    }

    public function testSetOutputInvalidTypeThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $job = new Job('* * * * *', 'php -v');
        $job->setOutput(123);
    }

    public function testGetOutputCreatesDirectory()
    {
        $tempDir = sys_get_temp_dir() . '/php-crond-test-' . uniqid();
        $outputFile = $tempDir . '/subdir/output.log';
        $job = new Job('* * * * *', 'php -v', '', $outputFile);
        // getOutput should create directory
        $this->assertEquals($outputFile, $job->getOutput());
        $this->assertDirectoryExists($tempDir . '/subdir');
        // Cleanup
        rmdir($tempDir . '/subdir');
        rmdir($tempDir);
    }

    public function testGetCommandLineWithoutDaemon()
    {
        $job = new Job('* * * * *', 'php -v', '', '/tmp/out.log');
        $commandLine = $job->getCommandLine(false);
        $this->assertStringContainsString('php -v', $commandLine);
        $this->assertStringContainsString('/tmp/out.log', $commandLine);
        $this->assertStringContainsString('>>', $commandLine);
    }

    public function testGetCommandLineWithDaemon()
    {
        $job = new Job('* * * * *', 'php -v', '', '/tmp/out.log');
        $commandLine = $job->getCommandLine(true);
        $this->assertStringContainsString('php -v', $commandLine);
        // Daemon suffix depends on OS
        if ('\\' === \DIRECTORY_SEPARATOR) {
            $this->assertStringStartsWith('start /b cmd /c', $commandLine);
        } else {
            $this->assertStringEndsWith(' &', $commandLine);
        }
    }

    public function testGetCommandLineWithoutOutput()
    {
        // daemon defaults to true, we need to test with daemon false to avoid OS-specific prefix
        $job = new Job('* * * * *', 'php -v');
        $commandLine = $job->getCommandLine(false);
        $this->assertEquals('php -v', $commandLine);
    }
}