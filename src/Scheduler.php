<?php
namespace Wangruyi\PhpCrond;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Wangruyi\PhpCrond\Parser\Moment;

class Scheduler
{
    const INTERVAL_SECONDS = 60;

    protected $jobs = [];
    protected $logger = null;

    private $name = '';

    public function __construct($name='')
    {
        $this->name = $name ?: 'scheduler';
    }

    public function addJobs($jobs)
    {
        foreach ($jobs as $job){
            $this->addJob($job);
        }
    }

    public function addJob(Job $job)
    {
        if (!Moment::validate($job->getMoment())){
            throw new \InvalidArgumentException('Invalid moment expression: ' . $job->getMoment());
        }

        return array_push($this->jobs, $job);
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getLogger()
    {
        if (is_null($this->logger)) {
            $logger = new Logger($this->name);
            $logHandler = new StreamHandler(STDOUT, Logger::INFO);
            $logger->pushHandler($logHandler);
            $this->logger = $logger;
        }

        return $this->logger;
    }

    public function run()
    {
        $logger = $this->getLogger();
        $us = 1000000; // The unified unit of time is microseconds.

        while (true){
            $now = round(microtime(true) * $us);
            $interval = self::INTERVAL_SECONDS * $us;
            $delay = $interval - $now % $interval;
            $executeTime = (new \DateTime())->setTimestamp(($now + $delay) / $us);
            usleep($delay);

            $logger->info('Scheduler wake up');
            foreach ($this->jobs as $job){
                if (!Moment::match($job->getMoment(), $executeTime)) continue;

                $context = ['Job' => $job->getName()];
                try{
                    $logger->info($job->getCommandLine(), $context);
                    Worker::run($job);
                }catch (\Exception $e){
                    $logger->error($e->getMessage(), $context);
                }
            }
            $logger->info('Scheduler end');
        }
    }
}
