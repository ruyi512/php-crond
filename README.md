## PHP-Crond

一个简单的PHP定时任务框架，通过创建子进程来执行定时任务

## 安装
    composer require wangruyi/php-crond

## 示例
```
$sch = new \Wangruyi\PhpCrond\Scheduler();

$job = new \Wangruyi\PhpCrond\Job('* * * * *', 'php echo.php -a=1001', 'echo', 'log/echo.log');
$sch->addJob($job);

$job2 = new \Wangruyi\PhpCrond\Job('*/2 * * * *', 'php -v', 'phpv', 'log/echo.log');
$sch->addJob($job2);

$job3 = new \Wangruyi\PhpCrond\Job('*/2 * * * *', 'php echo.php -a=3001', 'echo', 'log/echo.log');
$sch->addJob($job3);

$sch->run();
```

## Thinkphp使用示例
```
namespace app\common\command\system;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use Wangruyi\PhpCrond\Job;
use Wangruyi\PhpCrond\Scheduler;

class Schedule extends Command
{

    protected function configure()
    {
        $this->setName('schedule:run')
            ->setDescription('定时任务调度器入口');
    }

    protected function execute(Input $input, Output $output)
    {
        $sch = new Scheduler();
        $jobs = [
            new Job('*/10 * * * *', 'php think order:stat', 'stat', '/var/logs/order_stat.log'),
            new Job('4 0 * * *', 'php think order:settlement', 'settlement', '/var/logs/order_settlement.log'),
        ];
        $sch->addJobs($jobs);
        $sch->run();
    }

}
```