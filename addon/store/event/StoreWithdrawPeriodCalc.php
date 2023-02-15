<?php
namespace addon\store\event;
use addon\store\model\Settlement;
use Carbon\Carbon;
/**
 * 门店结算
 */
class StoreWithdrawPeriodCalc
{
    public function handle($params)
    {
        $model = new Settlement();
        $time  = Carbon::today()->timestamp;
        $res   = $model->settlement($params['relate_id'], $time);
        return $res;
    }
}