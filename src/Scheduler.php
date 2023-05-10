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

    public function addJob(Job $job)
    {
        return array_push($this->jobs, $job);
    }

    public function run()
    {
        $logger = new Logger($this->name);
        $logHandler = new StreamHandler(STDOUT);
        $logger->pushHandler($logHandler);

        while (true){
            $now = time();
            $waitSeconds = self::INTERVAL - $now % self::INTERVAL;
            $executeTime = (new \DateTime())->setTimestamp($now + $waitSeconds);
            sleep($waitSeconds);

            foreach ($this->jobs as $job){
                if (!Moment::match($job->getMoment(), $executeTime)) continue;

                $worker = new Worker($logger);
                $worker->run($job);
            }
        }
    }
}
