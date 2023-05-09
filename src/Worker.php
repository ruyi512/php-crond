<?php
namespace Wangruyi\PhpCrond;

class Worker
{

    public function submit(Job $job)
    {
        $descriptors = array(0 => array("pipe", "r"), 1 => array("pipe", "w"), 2 => array("pipe", "w"));
        $process = proc_open($job->getCommand(), $descriptors, $pipes, $job->getCwd());
        var_dump($job->getCommand());
    }
}