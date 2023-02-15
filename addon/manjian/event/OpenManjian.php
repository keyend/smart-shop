<?php
namespace addon\manjian\event;
use addon\manjian\model\Manjian;
/**
 * 启动活动
 */
class OpenManjian
{
    public function handle($params)
    {
        $manjian = new Manjian();
        $res     = $manjian->cronOpenManjian($params['relate_id']);
        return $res;
    }
}