<?php
namespace Wangruyi\PhpCrond\Parser;

class CommandLine
{

    public static function isWindows()
    {
        return '\\' === \DIRECTORY_SEPARATOR;
    }

    public static function build($command, $output=null, $daemon=true)
    {
        $line = $command;
        if (strpos('>>', $line) === false && $output){
            $line = $line . ' >> ' . $output . ' 2>&1';
        }

        if ($daemon){
            if (self::isWindows()){
                $line = 'start /b cmd /c '. $line ;     //windows
            }else {
                $line .= ' &';
            }
        }

        return $line;
    }

}
