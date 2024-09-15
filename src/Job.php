<?php
namespace Wangruyi\PhpCrond;

use Wangruyi\PhpCrond\Parser\CommandLine;

class Job
{
    protected $name;
    protected $moment;
    protected $command;
    protected $output;
    protected $cwd;

    public function __construct($moment, $command, $name='', $output='', $cwd=null)
    {
        $this->moment = $moment;
        $this->command = $command;
        $this->name = $name;
        $this->output = $output;
        $this->cwd = $cwd ?: getcwd();
    }

    public function getMoment()
    {
        return $this->moment;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOutput()
    {
        if (is_string($this->output)){
            $path = $this->output;
        }else{
            $path = $this->output->getFilePath();
        }

        if (strpos($path, \DIRECTORY_SEPARATOR, 1)){
            $this->mkdir(dirname($path));
        }

        return $path;
    }

    public function getCwd()
    {
        return $this->cwd;
    }

    public function getCommandLine($daemon=true)
    {
        return CommandLine::build($this->getCommand(), $this->getOutput(), $daemon);
    }

    public function mkdir($path)
    {
        if (!is_dir($path)) {
            $this->mkdir(dirname($path));
            mkdir($path, 0777);
        }
    }

}