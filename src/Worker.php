<?php
namespace Wangruyi\PhpCrond;

use Symfony\Component\Process\Process;

class Worker
{

    public function submit(Job $job)
    {
        var_dump($job->getCommand());
//        $descriptors = array(0 => array("pipe", "r"), 1 => array("pipe", "w"), 2 => array("pipe", "w"));
//        $process = proc_open($job->getCommand(), $descriptors, $pipes, $job->getCwd());
        $process = Process::fromShellCommandline($job->getCommand(), $job->getCwd());
        $process->run();
    }
}