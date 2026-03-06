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
        $this->setOutput($output);
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

    public function setOutput($output)
    {
        if (is_string($output)) {
            $this->output = new FileOutput($output);
        }else if ($output instanceof FileOutput){
            $this->output = $output;
        }else {
            throw new \InvalidArgumentException('Invalid output type');
        }
    }

    public function getOutput()
    {
        $path = $this->output->getFilePath();

        $dir = dirname($path);
        if ($dir && !is_dir($dir)) {
            mkdir($dir, 0777, true);
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

}
