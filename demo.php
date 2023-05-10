<?php
require __DIR__ .'/vendor/autoload.php';
require_once __DIR__ . '/src/Scheduler.php';
require_once __DIR__ . '/src/Job.php';
require_once __DIR__ . '/src/Parser/Moment.php';
require_once __DIR__ . '/src/Worker.php';

$sch = new \Wangruyi\PhpCrond\Scheduler();

$job = new \Wangruyi\PhpCrond\Job('* * * * *', 'php echo.php -a=1001', 'echo', __DIR__, 'log/echo.log');
$sch->addJob($job);

$job2 = new \Wangruyi\PhpCrond\Job('*/2 * * * *', 'php -v', 'phpv', __DIR__, 'log/echo.log');
$sch->addJob($job2);

$job3 = new \Wangruyi\PhpCrond\Job('*/2 * * * *', 'php echo.php -a=3001', 'echo', __DIR__, 'log/echo.log');
$sch->addJob($job3);

$sch->run();