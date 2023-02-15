<?php
namespace addon\store\shop\controller;
use addon\store\model\StoreAccount as StoreAccountModel;
use app\shop\controller\BaseShop;
/**
 * 门店设置控制器
 */
class Config extends BaseShop
{
    /**
     * 门店结算周期配置
     */
    public function index()
    {
        $store_account_model = new StoreAccountModel();
        if (request()->isAjax()) {
            $period_type = input('period_type', 3);
            $apply_open = input('apply_open', 0);
            if (!in_array($period_type, $store_account_model->period_types)) {
                return error(-1, '参数错误');
            }
            $data = [
                'period_type' => $period_type,
                'apply_open' => $apply_open
            ];
            $res  = $store_account_model->setStoreWithdrawConfig($this->site_id, $data);
            return $res;
        }
        $config_info = $store_account_model->getStoreWithdrawConfig($this->site_id);
        $this->assign('config_info', $config_info['data']);
        return $this->fetch("config/index");
    }
}