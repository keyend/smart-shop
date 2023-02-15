<?php
namespace addon\bargain\event;
use addon\bargain\model\Bargain;
/**
 * 启动活动
 */
class OpenBargain
{
    public function handle($params)
    {
        $pintuan = new Bargain();
        $res     = $pintuan->cronOpenBargain($params['relate_id']);
        return $res;
    }
}