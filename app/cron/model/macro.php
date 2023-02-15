<?php
namespace app\cron\model;
use think\facade\Db;

trait macro
{
    /**
     * 宏命令
     *
     * @var Array
     */
    private $commands = [];

    /**
     * 添加宏命令
     *
     * @param string $type
     * @param callable $callable
     * @return void
     */
    final public function macro($name, $callable)
    {
        $this->commands[$name] = $callable;
    }

    /**
     * 调用魔术方法
     *
     * @param string $name
     * @param mixed $arguments
     * @return object
     */
    public function __call($name, $arguments = [])
    {
        if (isset($this->commands[$name])) {
            $callable = $this->commands[$name];
            return $callable($arguments);
        }

        return call_user_func_array([Db::name($this->table), $name], $arguments);
    }
}