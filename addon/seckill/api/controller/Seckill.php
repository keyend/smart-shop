<?php
namespace addon\seckill\api\controller;
use addon\seckill\model\Seckill as SeckillModel;
use app\api\controller\BaseApi;
/**
 * 秒杀
 */
class Seckill extends BaseApi
{
    /**
     * 列表信息
     */
    public function lists()
    {
        $condition = [
            'site_id' => $this->site_id
        ];
        $order     = 'seckill_start_time asc';
        $field     = 'seckill_id,name,seckill_start_time,seckill_end_time';
        $seckill_model = new SeckillModel();
        $list          = $seckill_model->getSeckillList($condition, $field, $order, null);
        $list          = $list['data'];
        foreach ($list as $key => $val) {
            $val                                   = $seckill_model->transformSeckillTime($val);
            $list[$key]['seckill_start_time_show'] = "{$val['start_hour']}:{$val['start_minute']}:{$val['start_second']}";
            $list[$key]['seckill_end_time_show']   = "{$val['end_hour']}:{$val['end_minute']}:{$val['end_second']}";
        }
        $res = [
            'list' => $list
        ];
        return $this->response($this->success($res));
    }
}