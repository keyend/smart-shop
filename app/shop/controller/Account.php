<?php
namespace app\shop\controller;
use app\Request;
use app\model\web\Account as AccountModel;
use addon\fenxiao\model\FenxiaoData;
use app\model\member\MemberAccount as MemberAccountModel;
use app\model\order\Order as OrderModel;
use app\model\order\OrderCommon as OrderCommonModel;
class Account extends BaseShop
{
    public function dashboard()
    {
        $account_model = new AccountModel();
        //会员余额
        $member_balance_sum = $account_model->getMemberBalanceSum($this->site_id);
        $is_memberwithdraw  = addon_is_exit('memberwithdraw', $this->site_id);
        $this->assign('is_memberwithdraw', $is_memberwithdraw);
        if ($is_memberwithdraw == 1) {
            $this->assign('member_balance_sum', $member_balance_sum['data']);
        } else {
            $member_balance = number_format($member_balance_sum['data']['balance'] + $member_balance_sum['data']['balance_money'], 2, '.', '');
            $this->assign('member_balance', $member_balance);
        }
        //获取分销商账户统计
        $is_addon_fenxiao = addon_is_exit('fenxiao', $this->site_id);
        $this->assign('is_addon_fenxiao', $is_addon_fenxiao);
        if ($is_addon_fenxiao == 1) {
            $fenxiao_data_model = new FenxiaoData();
            $account_data       = $fenxiao_data_model->getFenxiaoAccountData($this->site_id);
            $this->assign('account_data', $account_data);
            //累计佣金
            $fenxiao_account = number_format($account_data['account'] + $account_data['account_withdraw'], 2, '.', '');
            $this->assign('fenxiao_account', $fenxiao_account);
            //分销订单总金额
            $fenxiao_order_money = $fenxiao_data_model->getFenxiaoOrderSum($this->site_id);
            $this->assign('fenxiao_order_money', $fenxiao_order_money);
        }
        $order_model = new OrderModel();
        //获取订单总额
        $order_total_money = $order_model->getOrderMoneySum(
            [
                ['site_id', '=', $this->site_id],
                ['order_status', '>', 0]
            ], 'order_money');
        $this->assign('order_total_money', number_format($order_total_money['data'], 2, '.', ''));
        //获取订单退款金额
        $refund_total_money = $order_model->getOrderMoneySum(
            [
                ['site_id', '=', $this->site_id],
//				[ 'refund_status', '<>', 0 ],
            ], 'refund_money');
        $this->assign('refund_total_money', number_format($refund_total_money['data'], 2, '.', ''));
        $common_model = new OrderCommonModel();
        //获取订单总数
        $order_total_count = $common_model->getOrderCount(
            [
                ['site_id', '=', $this->site_id],
                ['order_status', '>', 0]
            ]
        );
        $this->assign('order_total_count', $order_total_count['data']);
        //获取退款订单总数
        $refund_total_count = $common_model->getOrderCount(
            [
                ['site_id', '=', $this->site_id],
                ['refund_money', '>', 0],
            ]
        );
        $this->assign('refund_total_count', $refund_total_count['data']);
        return $this->fetch('account/dashboard');
    }

    public function lists(MemberAccountModel $model, Request $req)
    {
        if ($req->isAjax()) {
            $page      = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $condition = [];
            $condition[] = ['site_id', '=', $this->site_id];
            $start_time = input('start_time', '');
            $end_time = input('end_time', '');
            $field = '*';
            if ($start_time && $end_time) {
                $condition[] = ['create_time', 'between', [date_to_time($start_time), date_to_time($end_time)]];
            } elseif (!$start_time && $end_time) {
                $condition[] = ['create_time', '<=', date_to_time($end_time)];
            } elseif ($start_time && !$end_time) {
                $condition[] = ['create_time', '>=', date_to_time($start_time)];
            }
            $list      = $model->getMemberAccountPageList($condition, $page, $page_size, 'id DESC', $field);
            array_walk($list['data']['list'], function(&$item) use($model) {
                $item['account_title'] = $model->getAccountType($item['account_type']);
                $item['type_tag'] = is_numeric($item['type_tag']) ? $item['type_name'] : $item['type_tag'];
                return $item;
            });
            return $list;
        } else {
            return $this->fetch('account/lists');
        }
    }
}