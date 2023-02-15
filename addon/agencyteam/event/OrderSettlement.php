<?php
namespace addon\agencyteam\event;
use addon\agencyteam\model\AgencyTeamOrderModel;
use addon\agencyteam\model\AgencySettlementModel;
use app\job\EntityMQService;
use think\facade\Log;

class OrderSettlement
{
    public function handle($data)
    {
        if (isset($data['order_id']) && !empty($data['order_id'])) {
            EntityMQService::push(__CLASS__, ['handleAppend', $data['order_id']]);
        }
    }

    /**
     * 增加团队订单
     *
     * @param int $order_id
     * @return void
     */
    public function handleAppend($order_id)
    {
        try {
            $order_model = app()->make(AgencyTeamOrderModel::class);
            $order_data = $order_model->getInfo($order_id);
            if ($order_data['promotion_addon']) {
                // 已有参与活动
                return false;
            }
            // 添加待结算订单
            $order_model->add($order_data);
        } catch(\Exception $e) {
            Log::error('[团队订单添加失败]' . $e->getMessage());

            if (strpos($e->getMessage(), 'error: 2006 MySQL')!==false) {
                EntityMQService::push(__CLASS__, ['handleAppend', $order_id], 1);
            }

            return false;
        }

        try {
            // 获取下次结算时间
            $settlementTime = $order_model->getSettlementTaskTime($order_data);
            if ($settlementTime > 0) {
                EntityMQService::push(__CLASS__, ['handleSettlement', ['site_id' => $order_data['site_id']]], $settlementTime);
            }
        } catch(\Exception $e) {
            Log::error('[添加结算时间]' . $e->getMessage());
        }
    }

    /**
     * 订单结算
     *
     * @param array $args
     * @return void
     */
    public function handleSettlement($args = [])
    {
        try {
            app()->make(AgencySettlementModel::class)->settlement($args['site_id'], 'system.bot', time());
        } catch(\Exception $e) {
            Log::error('[团队订单结算失败]' . $e->getMessage());
        }
    }
}