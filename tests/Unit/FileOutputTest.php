<?php

namespace Wangruyi\PhpCrond\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wangruyi\PhpCrond\FileOutput;

class FileOutputTest extends TestCase
{
    public function testGetFilePathReturnsOriginalPathWhenNoPlaceholders()
    {
        $fileOutput = new FileOutput('/var/log/app.log');
        $this->assertEquals('/var/log/app.log', $fileOutput->getFilePath());
    }

    public function testGetFilePathReplacesDatePlaceholder()
    {
        $fileOutput = new FileOutput('/var/log/app-{Ymd}.log');
        $expected = '/var/log/app-' . date('Ymd') . '.log';
        $this->assertEquals($expected, $fileOutput->getFilePath());
    }

    public function testGetFilePathReplacesMultiplePlaceholders()
    {
        $fileOutput = new FileOutput('/var/log/{Y}/{m}/app-{d}.log');
        $expected = '/var/log/' . date('Y') . '/' . date('m') . '/app-' . date('d') . '.log';
        $this->assertEquals($expected, $fileOutput->getFilePath());
    }

    public function testParsePathWithEmptyString()
    {
        $fileOutput = new FileOutput('');
        $this->assertEquals('', $fileOutput->getFilePath());
    }
}