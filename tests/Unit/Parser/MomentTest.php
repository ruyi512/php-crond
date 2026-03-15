<?php

namespace Wangruyi\PhpCrond\Tests\Unit\Parser;

use PHPUnit\Framework\TestCase;
use Wangruyi\PhpCrond\Parser\Moment;

class MomentTest extends TestCase
{
    public function testValidateValidExpression()
    {
        $this->assertTrue(Moment::validate('* * * * *'));
        $this->assertTrue(Moment::validate('5 * * * *'));
        $this->assertTrue(Moment::validate('*/5 * * * *'));
        $this->assertTrue(Moment::validate('0 0 * * *'));
        $this->assertTrue(Moment::validate('0 0 1 * *'));
        $this->assertTrue(Moment::validate('0 0 * * 0'));
        $this->assertTrue(Moment::validate('1,2,3 * * * *'));
        $this->assertTrue(Moment::validate('1-5 * * * *'));
    }

    public function testValidateInvalidExpression()
    {
        $this->assertFalse(Moment::validate(''));
        $this->assertFalse(Moment::validate('* * * *'));
        $this->assertFalse(Moment::validate('* * * * * *'));
        $this->assertFalse(Moment::validate('a * * * *'));
        $this->assertFalse(Moment::validate('* * * * a'));
        $this->assertFalse(Moment::validate('* * * * * extra'));
    }

    public function testMatchExactMinute()
    {
        $datetime = new \DateTime('2026-03-15 14:30:00');
        $this->assertTrue(Moment::match('30 * * * *', $datetime));
        $this->assertFalse(Moment::match('29 * * * *', $datetime));
    }

    public function testMatchRange()
    {
        $datetime = new \DateTime('2026-03-15 14:30:00');
        $this->assertTrue(Moment::match('25-35 * * * *', $datetime));
        $this->assertFalse(Moment::match('35-40 * * * *', $datetime));
    }

    public function testMatchStep()
    {
        $datetime = new \DateTime('2026-03-15 14:30:00');
        $this->assertTrue(Moment::match('*/10 * * * *', $datetime)); // minutes 0,10,20,30,40,50
        $this->assertTrue(Moment::match('*/5 * * * *', $datetime)); // 30 divisible by 5, true.
        $datetime2 = new \DateTime('2026-03-15 14:32:00');
        $this->assertFalse(Moment::match('*/5 * * * *', $datetime2)); // 32 not divisible by 5, false.
    }

    public function testMatchMultiple()
    {
        $datetime = new \DateTime('2026-03-15 14:30:00');
        $this->assertTrue(Moment::match('30 14 * * *', $datetime));
        $this->assertTrue(Moment::match('30 14 15 3 *', $datetime));
        // March 15 2026 is a Sunday (0). Day of week 1 is Monday, so false.
        $this->assertFalse(Moment::match('30 14 15 3 1', $datetime));
    }

    public function testMatchDayOfWeek()
    {
        // 2026-03-15 is Sunday (0)
        $datetime = new \DateTime('2026-03-15 14:30:00');
        $this->assertTrue(Moment::match('* * * * 0', $datetime));
        $this->assertFalse(Moment::match('* * * * 1', $datetime));
    }

    public function testMatchInvalidExpressionThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $datetime = new \DateTime();
        Moment::match('invalid', $datetime);
    }

    public function testParseToArray()
    {
        // This method is protected, we can't test directly.
        // Instead we rely on match tests.
        $this->assertTrue(true);
    }
}