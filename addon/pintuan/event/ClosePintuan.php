<?php
namespace addon\pintuan\event;
use addon\pintuan\model\Pintuan;
/**
 * 关闭活动
 */
class ClosePintuan
{
    public function handle($params)
    {
        $pintuan = new Pintuan();
        $res     = $pintuan->cronClosePintuan($params['relate_id']);
        return $res;
    }
}