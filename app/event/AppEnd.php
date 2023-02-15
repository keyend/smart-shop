<?php
namespace app\event;
/**
 * 应用结束
 */
class AppEnd
{
    // 行为扩展的执行入口必须是run
    public function handle()
    {
        return success();
    }
}