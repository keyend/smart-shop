<?php
namespace app\job;
/**
 * 消费队列
 * @author Administrator
 * @suggest topthink/think-queue
 */
use think\queue\Job;
use think\facade\Queue;
use think\facade\Log;

class EntityMQService
{
    const name = 'EntityMQService';

    public function fire(Job $job, array $data)
    {
        $job->delete();

        list($className, $args) = $data;

        if (!class_exists($className)) {
            throw new \Exception("class not exists::$className");
        }

        try {
            list($method, $params) = $args;
            if (!is_array($params)) $params = [$params];
            call_user_func_array ([app()->make($className), $method], $params);
        } finally {
            ///// FINALLY /////
        }
    }

    /**
     * 加入自动执行队列
     *
     * @param string $boubleClass
     * @param array $args
     * @param integer $time
     * @return void
     */
    public static function push($boubleClass = '', $args = [], $time = 0)
    {
        if ($time === 0) {
            Queue::push(__CLASS__, [$boubleClass, $args], self::name);
        } else {
            // Log::write("添加事件 {$time} 后执行 {$boubleClass}:".json_encode($args));
            Queue::later($time, __CLASS__, [$boubleClass, $args], self::name);
        }
    }
}
