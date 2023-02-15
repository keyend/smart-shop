<?php
namespace addon\agencyteam\shop\controller;

use addon\agencyteam\model\AgencyTeamOrderModel;
use app\shop\controller\BaseShop;

/**
 * 团队订单
 */
class Order extends BaseShop
{
    /**
     * 视图输出字符串内容替换    相当于配置文件中的'view_replace_str'
     *
     * @var array
     */
    protected $replace = [];

    public function __construct()
    {
        parent::__construct();
        $this->replace = [
            'AGENCY_JS' => __ROOT__ . '/addon/agencyteam/shop/view/public/js',
            'AGENCY_CSS' => __ROOT__ . '/addon/agencyteam/shop/view/public/css'
        ];
    }

    /**
     * 团队订单
     *
     * @return void
     */
    public function lists(AgencyTeamOrderModel $model) 
    {
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $page_size  = input('page_size', PAGE_LIST_ROWS);
            $status     = input('status', 0);
            $condition  = [['ao.site_id', '=', $this->site_id]];

            if ($status == 3) {
                $condition[] = ['ao.is_bonus', '=', 1];
                $condition[] = ['ao.is_subsidy', '=', 1];
            }

            if (in_array($status, [2])) {
                $condition[] = ['ao.is_bonus', '=', '0'];
                $condition[] = ['ao.is_subsidy', '=', '0'];
            }

            $search_text_type = input('search_text_type', "order_name");//商品名称/订单编号
            $search_text      = input('search_text', "");

            if (!empty($search_text)) {
                $condition[] = ['o.' . $search_text_type, 'like', '%' . $search_text . '%'];
            }

            //下单时间
            $start_time = input('start_time', '');
            $end_time   = input('end_time', '');

            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ['o.create_time', '>=', date_to_time($start_time)];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ['o.create_time', '<=', date_to_time($end_time)];
            } elseif (!empty($start_time) && !empty(date_to_time($end_time))) {
                $condition[] = ['o.create_time', 'between', [date_to_time($start_time), date_to_time($end_time)]];
            }

            $list = $model->getOrderPageList($condition, $page_index, $page_size);
            return $list;
        } else {
            //订单状态
            return $this->fetch('order/lists', [], $this->replace);
        }
    }
}