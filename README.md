## PHP-Crond

一个简单的PHP定时任务框架，通过创建子进程来执行定时任务

## 安装
    composer require wangruyi/php-crond:dev-master

## 示例
```
$sch = new \Wangruyi\PhpCrond\Scheduler();

$job = new \Wangruyi\PhpCrond\Job('* * * * *', 'php echo.php -a=1001', 'echo', __DIR__, 'log/echo.log');
$sch->addJob($job);

$job2 = new \Wangruyi\PhpCrond\Job('*/2 * * * *', 'php -v', 'phpv', __DIR__, 'log/echo.log');
$sch->addJob($job2);

$job3 = new \Wangruyi\PhpCrond\Job('*/2 * * * *', 'php echo.php -a=3001', 'echo', __DIR__, 'log/echo.log');
$sch->addJob($job3);

$sch->run();
```