<?php
namespace addon\pintuan\event;
use addon\pintuan\model\Pintuan;
/**
 * 启动活动
 */
class OpenPintuan
{
    public function handle($params)
    {
        $pintuan = new Pintuan();
        $res     = $pintuan->cronOpenPintuan($params['relate_id']);
        return $res;
    }
}