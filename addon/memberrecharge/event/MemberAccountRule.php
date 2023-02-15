<?php
namespace addon\memberrecharge\event;
use addon\memberconsume\model\Consume;
/**
 * 会员账户变化规则
 */
class MemberAccountRule
{
    public function handle($data)
    {
//	    $config = new Consume();
//	    $info = $config->getConfig();
//        if($data['account'] == 'point')
//        {
//            if($info['data']['value']['is_return_point'] == 1)
//            {
//                if($info['data']['value']['return_point_status'] == 'receive')
//                {
//                    $data = "会员消费订单收货返积分,比率".$info['data']['value']['return_point_rate']."%";
//                }
//                if($info['data']['value']['return_point_status'] == 'pay')
//                {
//                    $data = "会员消费订单支付返积分,比率".$info['data']['value']['return_point_rate']."%";
//                }
//                if($info['data']['value']['return_point_status'] == 'complete')
//                {
//                    $data = "会员消费订单支付返积分,比率".$info['data']['value']['return_point_rate']."%";
//                }
//            }
//        }
//        if($data['account'] == 'growth')
//        {
//            if($info['data']['value']['is_return_point'] == 1)
//            {
//                if($info['data']['value']['return_point_status'] == 'receive')
//                {
//                    $data = "会员消费订单收货返成长值,比率".$info['data']['value']['return_growth_rate']."%";
//                }
//                if($info['data']['value']['return_point_status'] == 'pay')
//                {
//                    $data = "会员消费订单支付返成长值,比率".$info['data']['value']['return_growth_rate']."%";
//                }
//                if($info['data']['value']['return_point_status'] == 'complete')
//                {
//                    $data = "会员消费订单支付返成长值,比率".$info['data']['value']['return_growth_rate']."%";
//                }
//            }
//        }
//        return '';
    }
}