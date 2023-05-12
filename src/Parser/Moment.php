<?php
namespace Wangruyi\PhpCrond\Parser;


class Moment
{
    const MOMENT_ALWAYS = '*';
    const MOMENT_MATCHER = ['matchMinute', 'matchHour', 'matchDayOfMonth', 'matchMonth', 'matchDayOfWeek'];

    public static function validate($expression)
    {
        $moment = preg_split("/[\s]+/", $expression);
        if (count($moment) != count(self::MOMENT_MATCHER)){
            return false;
        }

        foreach ($moment as $item){
            if (!preg_match('/^[\*\-\,\/\d]+$/', $item)){
                return false;
            }
        }

        return true;
    }

    /**
     *  Example of $expression definition:
     *  .---------------- minute (0 - 59)
     *  |  .------------- hour (0 - 23)
     *  |  |  .---------- day of month (1 - 31)
     *  |  |  |  .------- month (1 - 12)
     *  |  |  |  |  .---- day of week (0 - 6) (0 for Sunday through 6 for Saturday)
     *  |  |  |  |  |
     *  *  *  *  *  *
     */
    public static function match($expression, \DateTime $datetime)
    {
        $moment = preg_split("/[\s]+/", $expression);
        if (count($moment) != count(self::MOMENT_MATCHER)){
            throw new \InvalidArgumentException('Invalid moment expression');
        }

        foreach ($moment as $i => $item){
            $matcher = self::MOMENT_MATCHER[$i];
            if (!self::$matcher($item, $datetime)){
                return false;
            }
        }

        return true;
    }

    public static function matchMinute($expression, \DateTime $datetime)
    {
        $minute = intval($datetime->format('i'));
        $validValues = self::parseToArray($expression, 0, 59);
        if (in_array($minute, $validValues)){
            return true;
        }

        return false;
    }

    public static function matchHour($expression, \DateTime $datetime)
    {
        $hour = intval($datetime->format('H'));
        $validValues = self::parseToArray($expression, 0, 23);
        if (in_array($hour, $validValues)){
            return true;
        }

        return false;
    }

    public static function matchDayOfMonth($expression, \DateTime $datetime)
    {
        $day = intval($datetime->format('j'));
        $validValues = self::parseToArray($expression, 1, 31);
        if (in_array($day, $validValues)){
            return true;
        }

        return false;
    }

    public static function matchMonth($expression, \DateTime $datetime)
    {
        $month = intval($datetime->format('n'));
        $validValues = self::parseToArray($expression, 1, 12);
        if (in_array($month, $validValues)){
            return true;
        }

        return false;
    }

    public static function matchDayOfWeek($expression, \DateTime $datetime)
    {
        $day = intval($datetime->format('w'));
        $validValues = self::parseToArray($expression, 0, 6);
        if (in_array($day, $validValues)){
            return true;
        }

        return false;
    }

    protected static function parseToArray($expression, $min, $max)
    {
        $values = array();

        $subExpressions = explode(',', $expression);
        foreach ($subExpressions as $exp) {
            $_exp = explode('/', $exp);
            $step = empty($_exp[1]) ? 1 : intval($_exp[1]);
            $_min = $_exp[0] == self::MOMENT_ALWAYS ? $min : intval($_exp[0]);
            $_max = $_exp[0] == self::MOMENT_ALWAYS ? $max : intval($_exp[0]);

            $__exp = explode('-', $_exp[0]);
            if (count($__exp) > 1){
                $_min = intval($__exp[0]);
                $_max = intval($__exp[1]);
            }

            for ($i = $_min; $i <= $_max; $i += $step) {
                $values[] = intval($i);
            }
        }

        return array_unique($values);
    }

}
