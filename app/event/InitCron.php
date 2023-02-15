<?php
namespace app\event;
use think\facade\Config;
use think\facade\Cache;
use think\facade\Log;
use app\job\EntityMQService;
/**
 * 初始化计划任务启动
 * @author Administrator
 *
 */
class InitCron
{
    // 任务名称
    const name = 'ww_b16';

    /**
     * 等待执行时间
     *
     * @var integer
     */
    private $delay = 0;

    public function handle()
    {
        $interval = redis()->get(self::name);
        if ($interval == null) {
            redis()->set(self::name, 0);
        }

        if (defined('BIND_MODULE') && BIND_MODULE === 'install') {
            return;
        }

        $last_time = Cache::get("cron_last_load_time");
        if (empty($last_time)) {
            $last_time = 0;
        }

        $module = request()->module();
        if ($module != 'cron') {
            if (!defined('CRON_EXECUTE') && time() - $last_time > 60) {
                define('CRON_EXECUTE', 1);
                $url = url('cron/task/phpCron');
                if (strpos($url, 'http:///') === FALSE) {
                    $this->appendTrigger($url);
                }
            }
        }
    }

    /**
     * 添加事件
     *
     * @param [type] $url
     * @return void
     */
    public function appendTrigger($url, $time = 0)
    {
        // 次数增加
        $interval = redis()->incr(self::name);

        // Log::write("异步事件队列长度 => {$interval}");
        if (0 <= $interval) {
            EntityMQService::push(__CLASS__, ['handleMQService', $url], $time);
        }
    }

    /**
     * 改同步为异步处理
     * 
     * @param array $args
     * @return void
     */
    public function handleMQService($url)
    {
        $this->ping(function($url) {
            $interval = redis()->decr(self::name);
            // Log::write("异步事件减少[{$url}] => {$interval}");
            if (0 <= $interval) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_TIMEOUT, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_exec($ch);
            }

            return $interval;
        }, $url);
    }

    /**
     * 心跳实现方法
     *
     * @return void
     */
    private function ping($callable, $url)
    {
        $interval = $callable($url);

        if ($interval === 0) {
            // 睡眼若干微秒
            $microtime = mt_rand(200000, 800000);
            usleep($microtime);

            // Log::write("添加自动 {$interval} <= 0 ~~ {$microtime}");
            // EntityMQService::push(__CLASS__, ['appendTrigger', [$url, 4]], 1);
            $this->appendTrigger($url, $this->delay);
        }
    }
}