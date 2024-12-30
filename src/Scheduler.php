<?php
namespace Wangruyi\PhpCrond;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Wangruyi\PhpCrond\Parser\Moment;

class Scheduler
{
    const INTERVAL = 60;

    protected $jobs = [];
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

    public function run()
    {
        $logger = new Logger($this->name);
        $logHandler = new StreamHandler(STDOUT, Logger::INFO);
        $logger->pushHandler($logHandler);

        while (true){
            $now = round(microtime(true) * 1000);
            $interval = self::INTERVAL * 1000;
            $waitMilliSeconds = $interval - $now % $interval;
            $executeTime = (new \DateTime())->setTimestamp(($now + $waitMilliSeconds) / 1000);
            usleep($waitMilliSeconds * 1000);

            $logger->info('Schedule start');
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
            $logger->info('Schedule end');
        }
    }
}
