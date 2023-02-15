<?php
namespace addon\pintuan\event;
use addon\pintuan\model\PintuanGroup;
/**
 * 关闭活动
 */
class ClosePintuanGroup
{
    public function handle($params)
    {
        $pintuan = new PintuanGroup();
        $res     = $pintuan->cronClosePintuanGroup($params['relate_id']);
        return $res;
    }
}