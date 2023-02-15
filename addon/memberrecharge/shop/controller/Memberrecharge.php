<?php
namespace addon\memberrecharge\shop\controller;
use addon\coupon\model\CouponType;
use addon\memberrecharge\model\MemberRechargeCard as MemberRechargeCardModel;
use addon\memberrecharge\model\Memberrecharge as MemberRechargeModel;
use addon\memberrecharge\model\MemberrechargeOrder as MemberRechargeOrderModel;
use app\shop\controller\BaseShop;
/**
 * 会员充值
 */
class Memberrecharge extends BaseShop
{
    //套餐字段
    protected $field = 'recharge_id,recharge_name,cover_img,face_value,buy_price,point,growth,coupon_id,sale_num,create_time,status';
    //开卡字段
    protected $card_field = 'card_id,recharge_id,card_account,cover_img,face_value,point,growth,coupon_id,buy_price,member_img,nickname,order_id,order_no,from_type,use_status,create_time,use_time';
    //订单字段
    protected $order_field = 'order_id,recharge_id,order_no,cover_img,face_value,buy_price,point,growth,coupon_id,price,pay_type,pay_type_name,status,create_time,pay_time,member_img,nickname';
    //优惠券字段
    protected $coupon_field = 'coupon_type_id,coupon_name,money,count,lead_count,max_fetch,at_least,end_time,image,validity_type,fixed_term';
    /**
     * 充值会员套餐列表
     * @return array|mixed
     */
    public function lists()
    {
        $model = new MemberRechargeModel();
        $condition = [];
        //获取续签信息
        if (request()->isAjax()) {
            $status = input('status', '');//套餐状态
            if ($status) {
                $condition[] = [ 'status', '=', $status ];
            }
            $condition[] = [ 'site_id', '=', $this->site_id ];
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $list = $model->getMemberRechargePageList($condition, $page, $page_size, 'recharge_id desc', $this->field);
            return $list;
        } else {
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $list = $model->getMemberRechargePageList($condition, 1, $page_size, 'recharge_id desc', $this->field);
            $this->assign('list', $list);
            $config = $model->getConfig($this->site_id);
            $config = $config[ 'data' ];
            $this->assign("config", $config);
            $this->forthMenu();
            return $this->fetch('memberrecharge/lists');
        }
    }
    /**
     * 添加充值套餐
     * @return array|mixed
     */
    public function add()
    {
        if (request()->isAjax()) {
            $data = [
                'site_id' => $this->site_id,
                'recharge_name' => input('recharge_name', ''),//套餐名称
                'cover_img' => input('cover_img', ''),//封面
                'face_value' => input('face_value', ''),//面值
                'buy_price' => input('buy_price', ''),//价格
                'point' => input('point', ''),//赠送积分
                'growth' => input('growth', ''),//赠送成长值
                'coupon_id' => input('coupon_id', '')//优惠券id
            ];
            $model = new MemberRechargeModel();
            return $model->addMemberRecharge($data);
        } else {
            //获取优惠券列表
            $coupon_model = new CouponType();
            $condition = [
                [ 'status', '=', 1 ],
                [ 'site_id', '=', $this->site_id ],
            ];
            $coupon_list = $coupon_model->getCouponTypeList($condition, $this->coupon_field);
            $this->assign('coupon_list', $coupon_list);
            return $this->fetch('memberrecharge/add');
        }
    }
    /**
     * 编辑充值套餐
     * @return array|mixed
     */
    public function edit()
    {
        $rechargeModel = new MemberRechargeModel();
        $recharge_id = input('recharge_id', '');
        if (request()->isAjax()) {
            $data = [
                'recharge_name' => input('recharge_name', ''),//套餐名称
                'cover_img' => input('cover_img', ''),//封面
                'face_value' => input('face_value', ''),//面值
                'buy_price' => input('buy_price', ''),//价格
                'point' => input('point', ''),//赠送积分
                'growth' => input('growth', ''),//赠送成长值
                'coupon_id' => input('coupon_id', '')//优惠券id
            ];
            return $rechargeModel->editMemberRecharge(
                [ [ 'recharge_id', '=', $recharge_id ] ]
                , $data
            );
        } else {
            //获取套餐详情
            $recharge = $rechargeModel->getMemberRechargeInfo(
                [ [ 'recharge_id', '=', $recharge_id ] ],
                $this->field
            );
            $this->assign('recharge', $recharge);
            //获取优惠券列表
            $coupon_model = new CouponType();
            $condition = [
                [ 'site_id', '=', $this->site_id ],
                [ 'status', '=', 1 ]
            ];
            $coupon_list = $coupon_model->getCouponTypeList($condition, $this->coupon_field);
            $this->assign('coupon_list', $coupon_list);
            return $this->fetch('memberrecharge/edit');
        }
    }
    /**
     * 充值套餐详情
     * @return mixed
     */
    public function detail()
    {
        $recharge_model = new MemberRechargeModel();
        $recharge_id = input('recharge_id', '');
        //获取套餐详情
        $recharge = $recharge_model->getMemberRechargeInfo(
            [ [ 'recharge_id', '=', $recharge_id ] ],
            $this->field
        );
        $this->assign('recharge', $recharge);
        return $this->fetch('memberrecharge/detail');
    }
    /**
     * 停用充值套餐
     * @return array
     */
    public function invalid()
    {
        $model = new MemberRechargeModel();
        $recharge_id = input('recharge_id', '');
        $data = [ 'status' => 2 ];
        $condition = [ [ 'recharge_id', '=', $recharge_id ] ];
        $res = $model->editMemberRecharge($condition, $data);
        return $res;
    }
    /**
     * 删除充值套餐
     * @return mixed
     */
    public function delete()
    {
        $model = new MemberRechargeModel();
        $recharge_id = input('recharge_id', '');
        return $model->deleteMemberRecharge([ [ 'recharge_id', '=', $recharge_id ] ]);
    }
    /**
     * 开卡列表
     * @return array|mixed
     */
    public function card_lists()
    {
        $model = new MemberRechargeCardModel();
        $condition[] = [ 'site_id', '=', $this->site_id ];
        //获取续签信息
        if (request()->isAjax()) {
            $status = input('use_status', '');//使用状态
            if ($status) {
                $condition[] = [ 'use_status', '=', $status ];
            }
            $site_name = input('site_name', '');
            if ($site_name) {
                $condition[] = [ 'site_name', 'like', '%' . $site_name . '%' ];
            }
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $list = $model->getMemberRechargeCardPageList($condition, $page, $page_size, 'card_id desc', $this->card_field);
            return $list;
        } else {
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $list = $model->getMemberRechargeCardPageList($condition, 1, $page_size, 'card_id desc', $this->card_field);
            $this->assign('list', $list);
            return $this->fetch('memberrecharge/card_lists');
        }
    }
    /**
     * 开卡详情
     * @return mixed
     */
    public function card_detail()
    {
        $model = new MemberRechargeCardModel();
        $card_id = input('card_id', '');
        //获取详情
        $card = $model->getMemberRechargeCardInfo(
            [ [ 'card_id', '=', $card_id ] ],
            $this->card_field
        );
        $this->assign('card', $card);
        return $this->fetch('memberrecharge/card_detail');
    }
    /**
     * 订单列表
     * @return array|mixed
     */
    public function order_lists()
    {
        $condition[] = [ 'site_id', '=', $this->site_id ];
        $model = new MemberRechargeOrderModel();
        //获取续签信息
        if (request()->isAjax()) {
            $condition[] = [ 'status', '=', 2 ];
            $site_name = input('site_name', '');
            if ($site_name) {
                $condition[] = [ 'site_name', 'like', '%' . $site_name . '%' ];
            }
            //支付时间
            $pay_start_time = input('pay_start_time', '');
            $pay_end_time = input('pay_end_time', '');
            if ($pay_start_time && $pay_end_time) {
                $condition[] = [ 'pay_time', 'between', [ $pay_start_time, $pay_end_time ] ];
            }
            //支付方式
            $pay_type = input('pay_type', '');
            if ($pay_type) {
                $condition[] = [ 'pay_type', '=', $pay_type ];
            }
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $list = $model->getMemberRechargeOrderPageList($condition, $page, $page_size, 'order_id desc', $this->order_field);
            return $list;
        } else {
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $list = $model->getMemberRechargeOrderPageList($condition, 1, $page_size, 'order_id desc', $this->order_field);
            $this->assign('list', $list);
            $this->forthMenu();
            return $this->fetch('memberrecharge/order_lists');
        }
    }
    /**
     * 订单详情
     * @return mixed
     */
    public function order_detail()
    {
        $model = new MemberRechargeOrderModel();
        $order_id = input('order_id', '');
        //获取详情
        $order = $model->getMemberRechargeOrderInfo(
            [ [ 'order_id', '=', $order_id ] ],
            $this->order_field
        );
        $this->assign('order', $order);
        return $this->fetch('memberrecharge/order_detail');
    }
    /**
     * 是否开启充值
     * @return mixed
     */
    public function setConfig()
    {
        $model = new MemberRechargeModel();
        $is_use = input('is_use', 0);
        $data = [];
        return $model->setConfig($data, $is_use, $this->site_id);
    }
}