<?php
namespace addon\printer\event;
use app\model\system\Cron;
/**
 * 订单打印
 */
class OrderPay
{
    public function handle($param)
    {
        $cron = new Cron();
        $cron->addCron(1, 0, "订单小票打印", "PrintOrder", time(), $param['order_id']);
    }
}