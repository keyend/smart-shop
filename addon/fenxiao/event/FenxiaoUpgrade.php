<?php
namespace addon\fenxiao\event;
use addon\fenxiao\model\Fenxiao;

/**
 * 分销商升级
 */
class FenxiaoUpgrade
{
    /**
     * 分销商升级
     * @param unknown $order
     * @return multitype:
     */
    public function handle($fenxiao_id)
    {
        if (!empty($fenxiao_id)) {
            $fenxiao = new Fenxiao();
            $fenxiao->fenxiaoUpgrade($fenxiao_id);
        }
    }
}