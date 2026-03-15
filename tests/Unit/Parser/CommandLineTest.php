<?php

namespace Wangruyi\PhpCrond\Tests\Unit\Parser;

use PHPUnit\Framework\TestCase;
use Wangruyi\PhpCrond\Parser\CommandLine;

class CommandLineTest extends TestCase
{
    public function testIsWindows()
    {
        $expected = '\\' === \DIRECTORY_SEPARATOR;
        $this->assertEquals($expected, CommandLine::isWindows());
    }

    public function testBuildWithoutOutput()
    {
        $command = 'php -v';
        $result = CommandLine::build($command, null, false);
        $this->assertEquals($command, $result);
    }

    public function testBuildWithOutput()
    {
        $command = 'php -v';
        $output = '/tmp/output.log';
        $result = CommandLine::build($command, $output, false);
        $this->assertStringContainsString($command, $result);
        $this->assertStringContainsString('>>', $result);
        $this->assertStringContainsString($output, $result);
        $this->assertStringContainsString('2>&1', $result);
    }

    public function testBuildWithOutputAlreadyContainsRedirect()
    {
        $command = 'php -v >> existing.log';
        $output = '/tmp/output.log';
        $result = CommandLine::build($command, $output, false);
        // Should not add another redirect
        $this->assertStringNotContainsString('>> ' . $output, $result);
        // Should keep original redirect
        $this->assertStringContainsString('>> existing.log', $result);
    }

    public function testBuildDaemonOnUnix()
    {
        if (CommandLine::isWindows()) {
            $this->markTestSkipped('Test only for non-Windows');
        }
        $command = 'php -v';
        $result = CommandLine::build($command, null, true);
        $this->assertStringEndsWith(' &', $result);
    }

    public function testBuildDaemonOffUnix()
    {
        if (CommandLine::isWindows()) {
            $this->markTestSkipped('Test only for non-Windows');
        }
        $command = 'php -v';
        $result = CommandLine::build($command, null, false);
        $this->assertEquals($command, $result);
    }

    public function testBuildDaemonOnWindows()
    {
        if (!CommandLine::isWindows()) {
            $this->markTestSkipped('Test only for Windows');
        }
        $command = 'php -v';
        $result = CommandLine::build($command, null, true);
        $this->assertStringStartsWith('start /b cmd /c ', $result);
        $this->assertStringContainsString($command, $result);
    }

    public function testBuildDaemonOffWindows()
    {
        if (!CommandLine::isWindows()) {
            $this->markTestSkipped('Test only for Windows');
        }
        $command = 'php -v';
        $result = CommandLine::build($command, null, false);
        $this->assertEquals($command, $result);
    }

    public function testBuildWithOutputAndDaemon()
    {
        $command = 'php -v';
        $output = '/tmp/out.log';
        $result = CommandLine::build($command, $output, true);
        $this->assertStringContainsString('>>', $result);
        $this->assertStringContainsString($output, $result);
        if (CommandLine::isWindows()) {
            $this->assertStringStartsWith('start /b cmd /c ', $result);
        } else {
            $this->assertStringEndsWith(' &', $result);
        }
    }
}