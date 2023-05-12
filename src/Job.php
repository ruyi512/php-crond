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
        return $this->output;
    }

    public function getCwd()
    {
        return $this->cwd;
    }

    public function getCommandLine($daemon=true)
    {
        return CommandLine::build($this->getCommand(), $this->getOutput(), $daemon);
    }

}