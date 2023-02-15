<?php
namespace addon\groupbuy\event;
use addon\groupbuy\model\Groupbuy;
/**
 * 关闭活动
 */
class CloseGroupbuy
{
    public function handle($params)
    {
        $groupbuy = new Groupbuy();
        $res      = $groupbuy->cronCloseGroupbuy($params['relate_id']);
        return $res;
    }
}