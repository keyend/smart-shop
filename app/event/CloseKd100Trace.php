<?php
namespace app\event;
use app\model\express\Kd100;
/**
 * 关闭物流查询
 * @author Administrator
 *
 */
class CloseKd100Trace
{
    public function handle($param)
    {
        $kd100_model = new Kd100();
        $result      = $kd100_model->modifyStatus(0, $param['site_id']);
        return $result;
    }
}