<?php
namespace addon\manjian\event;
use addon\manjian\model\Manjian;
/**
 * 关闭活动
 */
class CloseManjian
{
    public function handle($params)
    {
        $manjian = new Manjian();
        $res     = $manjian->cronCloseManjian($params['relate_id']);
        return $res;
    }
}