<?php
namespace Wangruyi\PhpCrond;

use Symfony\Component\Process\Process;
use Wangruyi\PhpCrond\Parser\CommandLine;

class Worker
{
    /**
     * Start execution by fork a daemon process
     *
     * @param Job $job
     */
    public static function run(Job $job)
    {
        $process = Process::fromShellCommandline($job->getCommandLine(), $job->getCwd());
        $process->run();
        if (!$process->isSuccessful()){
            throw new \RuntimeException($process->getErrorOutput());
        }
    }
}