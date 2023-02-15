<?php
namespace addon\bargain\event;
use addon\bargain\model\Bargain;
/**
 * 关闭活动
 */
class CloseBargain
{
    public function handle($params)
    {
        $bargain = new Bargain();
        $res     = $bargain->cronCloseBargain($params['relate_id']);
        return $res;
    }
}