<?php
namespace addon\store\store\controller;
use addon\store\model\StoreOrder;
use app\model\member\Register;
use app\model\order\OrderCommon;
use addon\store\model\StoreGoodsSku as StoreGoodsSkuModel;
use app\model\member\Member as MemberModel;
use app\model\system\Pay;
use think\facade\Session;
use app\model\goods\GoodsCategory as GoodsCategoryModel;
use app\model\goods\Goods as GoodsModel;
/**
 * 收银台
 * @author Administrator
 *
 */
class Cash extends BaseStore
{
    public $buyer_info = [];//买家
    public $default_member_info = [];//默认绑定会员
    public $resting_cart = [];//挂单
    public $cart = [];//购物车
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        $this->buyer_info = Session::get("cash_buyer_info") ?? [];
        $this->default_member_info = Session::get("default_member_info") ?? [];
        $this->resting_cart = Session::get("resting_cart") ?? [];
    }
    /**
     * 收银台
     */
    public function cash()
    {
        $store_order_model = new StoreOrder();
        $this->assign("pay_type_list", $store_order_model->getPayType($this->site_id));
        $this->assign("resting_cart", $this->resting_cart);
        $this->assign("buyer_info", $this->buyer_info);
        $cart = Session::get("cart") ?? [];
        if (!empty($cart)) {
            foreach ($cart as $k => $item) {
                $goods = new GoodsModel();
                $sku_info = $goods->getGoodsSkuInfo([ ['goods_state', '=', 1],['is_delete', '=', 0], ['sku_id', '=', $item['sku_id'] ], ['site_id', '=', $this->site_id] ], 'discount_price');
                if (!empty($sku_info['data'])) {
                    $cart[$k]['sku_price'] = $sku_info['data']['discount_price'];
                } else {
                    unset($cart[$k]);
                }
            }
            Session::set("cart", $cart);
        }
        $this->assign("cart", $cart);
        $goods_catrgory_model = new GoodsCategoryModel();
        $goods_catrgory_list = $goods_catrgory_model->getCategoryList([ [ 'site_id', "=", $this->site_id ], [ "pid", "=", 0 ], [ "is_show", "=", 1 ] ]);
        $this->assign("goods_catrgory_list", $goods_catrgory_list[ "data" ]);
        return $this->fetch("cash/cash", [], $this->replace);
    }
    /**
     * 商品列表
     * @return mixed
     */
    public function getGoodsSkuList()
    {
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', "");
            $category_id = input('category_id', '');
            $condition = [
                [ 'g.goods_state', '=', 1 ],
                [ 'g.is_delete', '=', 0 ],
                [ 'g.site_id', '=', $this->site_id ],
                [ 'g.is_virtual', "=", 0 ]
            ];
            if (!empty($search_text)) {
                $condition[] = [ 'gs.sku_name|gs.sku_no', 'like', '%' . $search_text . '%' ];
            }
            if (!empty($category_id)) {
                $condition[] = [ 'g.category_id', 'like', '%,' . $category_id . ',%' ];
            }
            $condition[] = [ "sgs.store_id", "=", $this->store_id ];
            $store_goods_model = new StoreGoodsSkuModel();
            $res = $store_goods_model->getGoodsSkuPageList($condition, $page_index, 0);
            return $res;
        }
    }
    /**
     * 登录会员
     * @return array
     */
    public function loginBuyer()
    {
        if (request()->isAjax()) {
            $member_search = input("member_search", '');
            $condition = [];
            $condition[] = [ 'mobile|email|username', '=', $member_search ];
            $condition[] = [ 'site_id', '=', $this->site_id ];
            $member_model = new MemberModel();
            $member_info_result = $member_model->getMemberInfo($condition);
            $member_info = $member_info_result[ "data" ];
            if (empty($member_info))
                return $member_model->error([], "当前号码还不是会员");
            Session::set("cash_buyer_info", $member_info);
            return $member_info_result;
        }
    }
    /**
     * 新增会员
     * @return array
     */
    public function addBuyer()
    {
        if (request()->isAjax()) {
            $username = input("username", '');
            $mobile = input("mobile", '');
            $condition = [];
            $condition[] = [ 'username', '=', $username ];
            $member_model = new MemberModel();
            $member_info_result = $member_model->getMemberInfo($condition);
            $member_info = $member_info_result[ "data" ];
            if (!empty($member_info)) {
                Session::set("cash_buyer_info", $member_info);
                return $member_info_result;
            }
            $register_model = new Register();
            $data = [ 'username' => $username ];
            $data[ 'site_id' ] = $this->site_id;
            if (empty($mobile)) {
                return $member_model->error([], "手机号不能为空");
            }
            if (!empty($mobile)) $data[ 'mobile' ] = $mobile;
            $result = $register_model->mobileRegister($data);
            if ($result[ "code" ] < 0)
                return $result;
            $member_id = $result[ "data" ];
            $member_model = new MemberModel();
            $member_info_result = $member_model->getMemberInfo([ [ "member_id", "=", $member_id ] ]);
            Session::set("cash_buyer_info", $member_info_result[ "data" ]);
            return $member_info_result;
        }
    }
    /**
     * 注销会员
     */
    public function logoutBuyer()
    {
        if (request()->isAjax()) {
            Session::set("cash_buyer_info", []);
            $member_model = new MemberModel();
            return $member_model->success();
        }
    }
    /**
     * 同步挂单数据
     */
    public function restingCart()
    {
        $json = input("json", "");
        $temp_array = [];
        if (!empty($json)) {
            $temp_array = json_decode($json, true);
        }
        Session::set("resting_cart", $temp_array);
    }
    /**
     * 购物车同步
     */
    public function cart()
    {
        $cart_json = input("cart_json", "");
        $temp_array = [];
        if (!empty($cart_json)) {
            $temp_array = json_decode($cart_json, true);
        }
        Session::set("cart", $temp_array);
    }
    /**
     * 零钱支付(调用线下支付)
     * @return \app\model\order\unknown|void
     */
    public function offlinePay()
    {
        $out_trade_no = input("out_trade_no", '');
        $order_common_model = new OrderCommon();
        $order_info_result = $order_common_model->getOrderInfo([ [ "out_trade_no", "=", $out_trade_no ] ], "order_id");
        $order_info = $order_info_result[ "data" ];
        if (empty($order_info))
            return $this->error([], "找不到符合当前交易编号的订单");
        $result = $order_common_model->orderOfflinePay($order_info[ "order_id" ]);
        return $result;
    }
    /**
     * 得到买家id
     * @return mixed
     */
    public function buyer()
    {
        //查看是否登陆了会员
        if (!empty($this->buyer_info)) {
            $buyer_member_id = $this->buyer_info[ "member_id" ];
        } else {
            $buyer_member_id = $this->default_member_info[ "member_id" ];
        }
        return $buyer_member_id;
    }
    /**
     * 支付
     */
    public function pay()
    {
        $pay_type = input("pay_type", "");
        $out_trade_no = input("out_trade_no", '');
        $pay_model = new Pay();
        $buyer_member_id = $this->buyer();
        $return_url = addon_url("store://store/index/index");
        $pay_result = $pay_model->pay($pay_type, $out_trade_no, "pc", $buyer_member_id, $return_url);
        return $pay_result;
    }
    /**
     *支付信息
     */
    public function payInfo()
    {
        $out_trade_no = input("out_trade_no", '');
        $pay_model = new Pay();
        $pay_result = $pay_model->getPayInfo($out_trade_no);
        return $pay_result;
    }
}