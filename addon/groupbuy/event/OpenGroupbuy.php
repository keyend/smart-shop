<?php
namespace addon\groupbuy\event;
use addon\groupbuy\model\Groupbuy;
/**
 * 启动活动
 */
class OpenGroupbuy
{
    public function handle($params)
    {
        $pintuan = new Groupbuy();
        $res     = $pintuan->cronOpenGroupbuy($params['relate_id']);
        return $res;
    }
}