<?php
namespace Wangruyi\PhpCrond;

use Symfony\Component\Process\Process;
use Wangruyi\PhpCrond\Parser\CommandLine;

class Worker
{
    private $logger;

    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Start execution by fork a daemon process
     *
     * @param Job $job
     */
    public function run(Job $job)
    {
        $commandLine = CommandLine::build($job->getCommand(), $job->getOutput(), true);
        $outputPrefix = '[Job ' . $job->getName() . '] ';
        $this->logger->info($outputPrefix . $commandLine);

        $process = Process::fromShellCommandline($commandLine, $job->getCwd());
        $process->run();
        if (!$process->isSuccessful()){
            $this->logger->error($outputPrefix . $process->getErrorOutput());
        }else{
            $this->logger->info($outputPrefix . 'fork process success');
        }
    }
}