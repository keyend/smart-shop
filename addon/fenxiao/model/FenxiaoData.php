<?php
namespace addon\fenxiao\model;
use app\model\BaseModel;
/**
 * 分销数据
 */
class FenxiaoData extends BaseModel
{
    /**
     * 分销商账户统计
     * @return array|mixed
     */
    public function getFenxiaoAccountData($site_id)
    {
        $field = 'sum(account) as account,sum(account_withdraw) as account_withdraw,sum(account_withdraw_apply) as account_withdraw_apply';
        $res   = model('fenxiao')->getInfo([['status', 'in', '1,-1'], ['site_id', '=', $site_id],['is_delete','=',0]], $field);
        if (empty($res) || $res['account'] == null) {
            $res['account'] = '0.00';
        }
        if (empty($res) || $res['account_withdraw'] == null) {
            $res['account_withdraw'] = '0.00';
        }
        if (empty($res) || $res['account_withdraw_apply'] == null) {
            $res['account_withdraw_apply'] = '0.00';
        }
        return $res;
    }
    /**
     * 获取分销商申请人数
     * @return mixed
     */
    public function getFenxiaoApplyCount($site_id)
    {
        $count = model('fenxiao_apply')->getCount([['status', '=', 1], ['site_id', '=', $site_id]]);
        return $count;
    }
    /**
     * 获取分销商人数
     * @return mixed
     */
    public function getFenxiaoCount($site_id)
    {
        $count = model('fenxiao')->getCount([['site_id', '=', $site_id],['is_delete','=',0]]);
        return $count;
    }
    /**
     * 统计分销订单总金额
     * @return mixed
     */
    public function getFenxiaoOrderSum($site_id)
    {
        $field = 'sum(real_goods_money) as real_goods_money';
        $res   = model('fenxiao_order')->getInfo([['is_refund', '=', '0'], ['site_id', '=', $site_id], ['is_settlement', '=', 1]], $field);
        if ($res['real_goods_money'] == null) {
            $res['real_goods_money'] = '0.00';
        }
        return $res;
    }
}