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
        $this->logger->info('Command ' . $commandLine);

        $process = Process::fromShellCommandline($commandLine, $job->getCwd());
        $process->run();
        if (!$process->isSuccessful()){
            $this->logger->error($process->getErrorOutput());
        }else{
            $this->logger->info('Success ' . $commandLine);
        }
    }
}