<?php
namespace app\api\controller;
use app\model\express\ExpressPackage;
use app\model\order\Order as OrderModel;
use app\model\order\OrderCommon as OrderCommonModel;
use app\model\order\OrderRefund as OrderRefundModel;
class Order extends BaseApi
{
    /**
     * 详情信息
     */
    public function detail()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $order_common_model = new OrderCommonModel();
        $order_id           = isset($this->params['order_id']) ? $this->params['order_id'] : 0;
        $result             = $order_common_model->getMemberOrderDetail($order_id, $this->member_id, $this->site_id);
        return $this->response($result);
    }
    /**
     * 列表信息
     */
    public function lists()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $order_common_model = new OrderCommonModel();
        $condition          = array(
            ["member_id", "=", $this->member_id],
            ["site_id", "=", $this->site_id],
            ["is_delete", '=', 0]
        );
        $order_status       = isset($this->params['order_status']) ? $this->params['order_status'] : 'all';
        switch ($order_status) {
            case "waitpay"://待付款
                $condition[] = ["order_status", "=", 0];
                break;
            case "waitsend"://待发货
                $condition[] = ["order_status", "=", 1];
                break;
            case "waitconfirm"://待收货
                $condition[] = ["order_status", "in", [2, 3]];
                break;
            case "waitrate"://待评价
                $condition[] = ["order_status", "in", [4, 10]];
                $condition[] = ["is_evaluate", "=", 1];
                break;
        }
//		if (c !== "all") {
//			$condition[] = [ "order_status", "=", $order_status ];
//		}
        $page_index = isset($this->params['page']) ? $this->params['page'] : 1;
        $page_size  = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
        $res        = $order_common_model->getMemberOrderPageList($condition, $page_index, $page_size, "create_time desc");
        return $this->response($res);
    }
    /**
     * 订单评价基础信息
     */
    public function evluateinfo()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $order_id = isset($this->params['order_id']) ? $this->params['order_id'] : 0;
        if (empty($order_id)) {
            return $this->response($this->error('', 'REQUEST_ORDER_ID'));
        }
        $order_common_model = new OrderCommonModel();
        $order_info         = $order_common_model->getOrderInfo([
            ['order_id', '=', $order_id],
            ['member_id', '=', $token['data']['member_id']],
            ['order_status', 'in', ('4,10')],
            ['is_evaluate', '=', 1],
        ], 'evaluate_status,evaluate_status_name');
        $res = $order_info['data'];
        if (!empty($res)) {
            if ($res['evaluate_status'] == 2) {
                return $this->response($this->error('', '该订单已评价'));
            } else {
                $condition   = [
                    ['order_id', '=', $order_id],
                    ['member_id', '=', $token['data']['member_id']],
                    ['refund_status', '<>', 3],
                ];
                $list        = $order_common_model->getOrderGoodsList($condition, 'order_goods_id,order_id,order_no,site_id,member_id,goods_id,sku_id,sku_name,sku_image,price,num');
                $list        = $list['data'];
                $res['list'] = $list;
                return $this->response($this->success($res));
            }
        } else {
            return $this->response($this->error('', '没有找到该订单'));
        }
    }
    /**
     * 订单收货(收到所有货物)
     */
    public function takeDelivery()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $order_id = isset($this->params['order_id']) ? $this->params['order_id'] : 0;
        if (empty($order_id)) {
            return $this->response($this->error('', 'REQUEST_ORDER_ID'));
        }
        $order_model = new OrderCommonModel();
        $result      = $order_model->orderCommonTakeDelivery($order_id);
        return $this->response($result);
    }
    /**
     * 关闭订单
     */
    public function close()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $order_id = isset($this->params['order_id']) ? $this->params['order_id'] : 0;
        if (empty($order_id)) {
            return $this->response($this->error('', 'REQUEST_ORDER_ID'));
        }
        $order_model = new OrderModel();
        $result      = $order_model->orderClose($order_id);
        return $this->response($result);
    }
    /**
     * 获取订单数量
     */
    public function num()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        if (empty($this->params['order_status'])) {
            return $this->response($this->error('', 'REQUEST_ORDER_STATUS'));
        }
        $order_common_model = new OrderCommonModel();
        $order_refund_model = new OrderRefundModel();
        $data = [];
        foreach (explode(',', $this->params['order_status']) as $order_status) {
            $condition = array(
                ["member_id", "=", $this->member_id],
            );
            switch ($order_status) {
                case "waitpay"://待付款
                    $condition[] = ["order_status", "=", 0];
                    break;
                case "waitsend"://待发货
                    $condition[] = ["order_status", "=", 1];
                    break;
                case "waitconfirm"://待收货
                    $condition[] = ["order_status", "in", [2, 3]];
                    break;
                case "waitrate"://待评价
                    $condition[] = ["order_status", "in", [4, 10]];
                    $condition[] = ["is_evaluate", "=", 1];
                    break;
            }
            if ($order_status == 'refunding') {
                $result              = $order_refund_model->getRefundOrderGoodsCount([
                    ["member_id", "=", $this->member_id],
                    ["refund_status", "not in", [0, 3]]
                ]);
                $data[$order_status] = $result['data'];
            } else {
                $result              = $order_common_model->getOrderCount($condition);
                $data[$order_status] = $result['data'];
            }
        }
        return $this->response(success(0, '', $data));
    }
    /**
     * 订单包裹信息
     */
    public function package()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $order_id              = isset($this->params['order_id']) ? $this->params['order_id'] : '';//订单id
        $express_package_model = new ExpressPackage();
        $condition             = array(
            ["member_id", "=", $this->member_id],
            ["order_id", "=", $order_id],
        );
        $result                = $express_package_model->package($condition);
        if ($result) return $this->response($this->success($result));
        else return $this->response($this->error());
    }
    /**
     * 订单支付
     * @return string
     */
    public function pay()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $order_datas = json_decode($this->params['orderData'], true);
        if (empty($order_datas)) return $this->response($this->error('', "订单数据为空"));
        $order_ids = "";
        // 启用余额支付
        foreach($order_datas as $order_data) {
            if (isset($order_data['is_balance']) && $order_data['is_balance']) {
                $data = $this->filterOrderData($order_data);
                $result = app()->make(\app\model\order\OrderCreate::class)->pay($data);
            } else {
                $order_ids = string_split($order_ids, ",", $order_data['order_id']);
            }
        }
        // 正常支付
        if (!empty($order_ids)) {
            $order_common_model = new OrderCommonModel();
            $result             = $order_common_model->splitOrderPay($order_ids);
        }
        return $this->response($result);
    }
    /**
     * 过滤订单支付参数
     *
     * @param array $params
     * @return void
     */
    private function filterOrderData($params = [])
    {
        $data = [
            'site_id'                 => $this->site_id,
            'member_id'               => $this->member_id,
            'order_id'                => $params['order_id'],
            'out_trade_no'            => $params['out_trade_no'],
            'pay_password'            => $this->params['pay_password'],
            'is_balance'              => isset($params['is_balance']) ? $params['is_balance'] : 0,
            'is_point'                => 0,
            'order_from'              => $this->params['app_type'],
            'order_from_name'         => $this->params['app_type_name'],
            'delivery'                => $this->getDelivery($params),
            'coupon'                  => $this->getCoupon($params),
            'member_address'          => $this->getAddress($params),
            'sku'                     => $this->getSku($params),
            'latitude'                => $params["latitude"] ?? null,
            'longitude'               => $params["longitude"] ?? null,
            'is_invoice'              => $params["is_invoice"] ?? 0,
            'invoice_type'            => $params["invoice_type"] ?? 0,
            'invoice_title'           => $params["invoice_title"] ?? '',
            'taxpayer_number'         => $params["taxpayer_number"] ?? '',
            'invoice_content'         => $params["invoice_content"] ?? '',
            'invoice_full_address'    => $params["invoice_full_address"] ?? '',
            'is_tax_invoice'          => $params["is_tax_invoice"] ?? 0,
            'invoice_email'           => $params["invoice_email"] ?? '',
            'invoice_title_type'      => $params["invoice_title_type"] ?? 0,
            'buyer_ask_delivery_time' => $params["buyer_ask_delivery_time"] ?? '',
        ];
        return $data;
    }
    /**
     * 返回配送信息
     *
     * @param array $params
     * @return void
     */
    private function getDelivery($params = [])
    {
        $res = [];
        foreach($params as $key => $val) {
            if (strpos($key, 'delivery') !== FALSE) $res[$key] = $val;
        }
        return $res;
    }
    /**
     * 返回优惠券信息
     *
     * @param array $params
     * @return void
     */
    private function getCoupon($params = [])
    {
        $res = [];
        foreach($params as $key => $val) {
            if (strpos($key, 'coupon') !== FALSE) $res[$key] = $val;
        }
        return $res;
    }
    /**
     * 返回优惠券信息
     *
     * @param array $params
     * @return void
     */
    private function getAddress($params = [])
    {
        $res = [];
        foreach($params as $key => $val) {
            if (strpos($key, 'address') !== FALSE || in_array($val, ['province_id','city_id','district_id','community_id','name','mobile','telephone'])) {
                $res[$key] = $val;
            }
        }
        return $res;
    }
    /**
     * 返回SKU信息
     *
     * @param array $params
     * @return void
     */
    private function getSku($params = [])
    {
        $res = [];
        foreach($params['order_goods'] as $v) {
            $res[] = [
                'sku_id' => $v['sku_id'],
                'num' => $v['num']
            ];
        }
        return $res;
    }
}