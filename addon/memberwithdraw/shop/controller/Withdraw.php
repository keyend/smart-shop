<?php
namespace addon\memberwithdraw\shop\controller;
use addon\memberwithdraw\model\Withdraw as WithdrawModel;
use app\shop\controller\BaseShop;
/**
 * 会员提现
 */
class Withdraw extends BaseShop
{
    /**
     * 转账
     */
    public function transfer()
    {
        if (request()->isAjax()) {
            $id             = input('id', 0);
            $withdraw_model = new WithdrawModel();
            $result         = $withdraw_model->transfer($id);
            return $result;
        }
    }
}