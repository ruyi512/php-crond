# PHP-Crond

一个简单但强大的PHP定时任务调度框架，通过创建子进程执行定时任务，支持Cron表达式，轻量级且易于集成到现有项目中。

## 特性

- 支持标准Cron表达式（分、时、日、月、星期）
- 使用子进程执行任务，避免阻塞主进程
- 内置日志轮转（可选的按日期分割日志文件）
- 兼容Windows和Unix-like系统
- 易于与ThinkPHP、Laravel等框架集成
- 提供灵活的Job配置（任务名称、工作目录、输出文件等）

## 安装

```bash
composer require wangruyi/php-crond
```

## 快速开始

### 基本使用

```php
<?php

use Wangruyi\PhpCrond\Scheduler;
use Wangruyi\PhpCrond\Job;

$sch = new Scheduler();

$job = new Job('* * * * *', 'php echo.php -a=1001', 'echo', 'log/echo.log');
$sch->addJob($job);

$job2 = new Job('*/2 * * * *', 'php -v', 'phpv', 'log/echo.log');
$sch->addJob($job2);

$sch->run();
```

### Job构造函数参数说明

`Job`类的构造函数签名如下：

```php
public function __construct($moment, $command, $name='', $output='', $cwd=null)
```

- **$moment**：Cron表达式，如 `* * * * *` 表示每分钟执行。
- **$command**：需要执行的Shell命令。
- **$name**：任务名称，用于标识任务（可选）。
- **$output**：输出日志文件路径。如果留空，输出将被丢弃。支持日期变量，例如 `log/echo_{Ymd}.log`，详见下文。
- **$cwd**：执行命令时的工作目录（可选，默认当前目录）。

### 输出日志与FileOutput

框架内置了 `FileOutput` 类，可以自动按日期分割日志文件。在`$output`参数中使用 `{Ymd}` 等占位符即可启用日期分割。例如：

```php
$job = new Job('* * * * *', 'php echo.php', 'task', 'log/echo_{Ymd}.log');
```

这将在 `log` 目录下生成类似 `echo_20250101.log` 的日志文件。

如果不使用占位符，日志将始终写入同一个文件。

### setOutput 方法

`Job` 类还提供了 `setOutput` 方法，允许在运行时动态更改输出文件路径。该方法同样支持日期变量替换，用法与构造函数中的 `$output` 参数一致。例如：

```php
$job->setOutput('log/another_{Ymd}.log');
```

### 调度器 Scheduler

`Scheduler` 负责管理所有任务，并在无限循环中检查并执行到达时间的任务。

主要方法：

- `addJob(Job $job)`：添加单个任务。
- `addJobs(array $jobs)`：批量添加任务。
- `run()`：启动调度器，将一直运行直到被手动终止。

## 与ThinkPHP集成示例

ThinkPHP用户可以在自定义Command中调用调度器：

```php
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

## 注意事项

1. **运行环境**：本框架仅能在CLI模式下运行，不可用于Web请求。
2. **Windows支持**：在Windows系统下，命令构建会自动适配（使用 `cmd /C`），但复杂命令可能需要测试。
3. **日志目录**：请确保输出日志文件所在的目录存在且可写，否则任务可能执行失败。
4. **长时间运行**：调度器启动后会无限循环执行，请使用进程管理工具（如Supervisor）来管理服务。
5. **Cron表达式**：仅支持到分钟级别，秒级不支持。

## 故障排查

- 如果任务没有按预期执行，请检查Cron表达式是否正确，以及系统时间是否准确。
- 查看日志文件是否有输出，如果日志文件没有生成，可能是权限问题。
- 在Windows上，确保`php`命令在环境变量中可用。

## 贡献

欢迎提交Issue和Pull Request来改进本项目。

## 许可证

本项目基于MIT许可证开源。
