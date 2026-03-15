<?php

namespace Wangruyi\PhpCrond\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wangruyi\PhpCrond\DailyFileOutput;

class DailyFileOutputTest extends TestCase
{
    public function testGetFilePathAppendsDateWithDefaultFormat()
    {
        $fileOutput = new DailyFileOutput('app.log');
        $path = $fileOutput->getFilePath();
        $this->assertStringEndsWith('app-' . date('Ymd') . '.log', $path);
        // Directory part should be '.' (current directory)
        $this->assertStringStartsWith('.' . DIRECTORY_SEPARATOR, $path);
    }

    public function testGetFilePathWithCustomDateFormat()
    {
        $fileOutput = new DailyFileOutput('app.log', 'Y-m-d');
        $path = $fileOutput->getFilePath();
        $this->assertStringEndsWith('app-' . date('Y-m-d') . '.log', $path);
        $this->assertStringStartsWith('.' . DIRECTORY_SEPARATOR, $path);
    }

    public function testGetFilePathWithDirectory()
    {
        $fileOutput = new DailyFileOutput('/var/log/app.log');
        $path = $fileOutput->getFilePath();
        // Directory part should be /var/log (normalized)
        $expectedDir = str_replace('/', DIRECTORY_SEPARATOR, '/var/log');
        if ('\\' !== \DIRECTORY_SEPARATOR) {
            $this->assertStringStartsWith($expectedDir . DIRECTORY_SEPARATOR, $path);
        }
        $this->assertStringEndsWith('app-' . date('Ymd') . '.log', $path);
    }

    public function testGetFilePathWithoutExtension()
    {
        $fileOutput = new DailyFileOutput('app');
        $path = $fileOutput->getFilePath();
        $this->assertStringEndsWith('app-' . date('Ymd'), $path);
        $this->assertStringStartsWith('.' . DIRECTORY_SEPARATOR, $path);
    }

    public function testGetFilePathWithComplexPath()
    {
        $fileOutput = new DailyFileOutput('/var/log/app/error.log');
        $path = $fileOutput->getFilePath();
        $expectedDir = str_replace('/', DIRECTORY_SEPARATOR, '/var/log/app');
        if ('\\' !== \DIRECTORY_SEPARATOR) {
            $this->assertStringStartsWith($expectedDir . DIRECTORY_SEPARATOR, $path);
        }
        $this->assertStringEndsWith('error-' . date('Ymd') . '.log', $path);
    }
}