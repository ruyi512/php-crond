<?php
namespace Wangruyi\PhpCrond;
require __DIR__ .'/../vendor/autoload.php';
require_once 'Job.php';
require_once 'Moment.php';
require_once 'Worker.php';

class Scheduler
{
    const INTERVAL = 60;
    protected $jobs = [];

    public function addJob(Job $job)
    {
        return array_push($this->jobs, $job);
    }

    public function run()
    {
        while (true){
            $now = time();
            $waitSeconds = self::INTERVAL - $now % self::INTERVAL;
            $executeTime = (new \DateTime())->setTimestamp($now + $waitSeconds);
            sleep($waitSeconds);

            foreach ($this->jobs as $job){
                if (!Moment::match($job->getMoment(), $executeTime)) continue;

                $worker = new Worker();
                $worker->submit($job);
            }
        }
    }
}

$sch = new Scheduler;

$job = new Job('* * * * *', 'php Echo.php -a=1001', 'echo', __DIR__, '../log/echo.log');
$sch->addJob($job);

$job2 = new Job('*/2 * * * *', 'php -v', 'phpv', __DIR__, '../log/echo.log');
$sch->addJob($job2);

$job3 = new Job('*/2 * * * *', 'php Echo.php -a=3001', 'echo', __DIR__, '../log/echo.log');
$sch->addJob($job3);

$sch->run();