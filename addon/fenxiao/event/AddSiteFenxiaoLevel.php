<?php
namespace addon\fenxiao\event;
use addon\fenxiao\model\FenxiaoLevel as FenxiaoLevelModel;

/**
 * 增加默认分销商等级
 */
class AddSiteFenxiaoLevel
{

    public function handle($param)
    {
        if (!empty($param['site_id'])) {

            $model = new FenxiaoLevelModel();
            $default_level = $model->getLevelInfo([ ['site_id', '=', $param['site_id']], ['is_default', '=', 1] ], 'level_id');

            if (empty($default_level['data'])) {
                $data = [
                    'site_id' => $param['site_id'],
                    'level_name' => '默认等级',
                    'level_num' => 0,
                    'one_rate' => '',
                    'two_rate' => '',
                    'three_rate' => '',
                    'upgrade_type' => '2',
                    'fenxiao_order_num' => '',
                    'fenxiao_order_meney' => '',
                    'one_fenxiao_order_num' => '',
                    'one_fenxiao_order_money' => '',
                    'order_num' => '',
                    'order_money'  => '',
                    'child_num' => '',
                    'child_fenxiao_num' => '',
                    'one_child_num' => '',
                    'one_child_fenxiao_num' => '',
                    'is_default' => 1
                ];
                $res  = $model->addLevel($data);
                return $res;
            }
        }
    }

}