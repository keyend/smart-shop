<?php
namespace addon\printer\event;
use addon\printer\model\PrinterOrder;
/**
 * 关闭活动
 */
class PrintOrder
{
    public function handle($params)
    {
        $printer_order_model = new PrinterOrder();
        return $printer_order_model->printOrder($params['relate_id']);
    }
}