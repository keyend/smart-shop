<?php
namespace addon\fenxiao\model;
use app\model\BaseModel;
use app\model\member\Member;
use app\model\member\MemberAccount;
use app\model\message\Message;
use app\model\message\Sms;
use app\model\shop\ShopAcceptMessage;
use addon\wechat\model\Message as WechatMessage;
use app\model\member\Member as MemberModel;
/**
 * 分销商提现
 */
class FenxiaoWithdraw extends BaseModel
{
	//提现类型
	public $withdraw_type = [
		'balance' => '余额',
		'weixin' => '微信',
		'alipay' => '支付宝',
		'bank' => '银行卡',
	];
	
	/**
	 * 分销商申请提现
	 * @param $data
	 * @return array
	 */
	public function addFenxiaoWithdraw($data)
	{
        //获取分销商信息
        $fenxiao_model = new Fenxiao();
        $fenxiao_info = $fenxiao_model->getFenxiaoInfo([ [ 'member_id', '=', $data['member_id'] ] ], 'fenxiao_id,fenxiao_name,account');
        if(empty($fenxiao_info['data'])){
            return $this->error('该分销商不存在');
        }
        if($fenxiao_info['data']['account'] < $data['money']){
            return $this->error('', '提现金额大于可提现金额');
        }
        //获取提现配置信息
		$config_model = new Config();
		$config = $config_model->getFenxiaoWithdrawConfig($data['site_id']);
		$config_info = $config['data']['value'];
		if ($config_info['withdraw'] > $data['money']) {
			return $this->error('', '提现金额小于最低提现金额');
		}
		if ($data['money'] >= $config_info['min_no_fee'] && $data['money'] <= $config_info['max_no_fee']) {
			$data['withdraw_rate'] = 0;
			$data['withdraw_rate_money'] = 0;
			$data['real_money'] = $data['money'];
		} else {
			$data['withdraw_rate'] = $config_info['withdraw_rate'];
			if ($config_info['withdraw_rate'] == 0) {
				$data['withdraw_rate'] = 0;
				$data['withdraw_rate_money'] = 0;
				$data['real_money'] = $data['money'];
			} else {
				$data['withdraw_rate'] = $config_info['withdraw_rate'];
				$data['withdraw_rate_money'] = round($data['money'] * $config_info['withdraw_rate'] / 100, 2);
				$data['real_money'] = $data['money'] - $data['withdraw_rate_money'];
			}
		}
		
		$data['withdraw_no'] = date('YmdHis') . rand(1000, 9999);
		$data['create_time'] = time();
		
		model('fenxiao_withdraw')->startTrans();
		try {
			
			$data['fenxiao_id'] = $fenxiao_info['data']['fenxiao_id'];
			$data['fenxiao_name'] = $fenxiao_info['data']['fenxiao_name'];
			
			$res = model('fenxiao_withdraw')->add($data);
            //修改分销商提现中金额
            model('fenxiao')->setInc([ [ 'member_id', '=', $data['member_id'] ] ], 'account_withdraw_apply', $data['money']);
            //修改分销商可提现金额
            model('fenxiao')->setDec([ [ 'member_id', '=', $data['member_id'] ] ], 'account', $data['money']);
			//判断是否需要审核
			if ($config_info['withdraw_status'] == 2) {//不需要
				$result = $this->withdrawPass($res, $data['site_id']);
				if ($result['code'] < 0) {
					model('fenxiao_withdraw')->rollback();
					return $result;
				}
			}
			model('fenxiao_withdraw')->commit();
            //申请提现发送消息
            $data['keywords'] = 'FENXIAO_WITHDRAWAL_APPLY';
            $message_model = new Message();
            $message_model->sendMessage($data);
			return $this->success($res);
		} catch (\Exception $e) {
			model('fenxiao_withdraw')->rollback();
			return $this->error('', $e->getMessage());
		}
		
	}
	
	/**
	 * 提现审核通过
	 * @param $id
	 * @param $site_id
	 * @return array
	 */
	public function withdrawPass($ids, $site_id)
	{
		model('fenxiao_withdraw')->startTrans();
		try {
			$withdraw_list = $this->getFenxiaoWithdrawList([ [ 'id', 'in', $ids ] ], '*');
			foreach ($withdraw_list['data'] as $k => $v) {
				if($v['status'] == 1){
                    //修改分销商提现中金额
                    model('fenxiao')->setDec([ [ 'fenxiao_id', '=', $v['fenxiao_id'] ] ], 'account_withdraw_apply', $v['money']);
                    //修改分销商已提现金额
                    model('fenxiao')->setInc([ [ 'fenxiao_id', '=', $v['fenxiao_id'] ] ], 'account_withdraw', $v['money']);
                    //添加会员账户流水
                    $member_account = new MemberAccount();
                    $member_result = $member_account->addMemberAccount($site_id, $v['member_id'], 'balance_money', $v['real_money'], 'fenxiao', '佣金提现', '分销佣金提现');
                    if ($member_result['code'] != 0) {
                        model('fenxiao_withdraw')->rollback();
                        return $member_result;
                    }
                    $account_model = new FenxiaoAccount();
                    $account_result = $account_model->addAccountLog($v['fenxiao_id'], $v['fenxiao_name'], 'withdraw', '-' . $v['money'], $v['id']);
                    if ($account_result['code'] != 0) {
                        model('fenxiao_withdraw')->rollback();
                        return $account_result;
                    }
                }
			}
            //修改提现状态
            $data = [
                'status' => 2,
                'payment_time' => time(),
                'modify_time' => time(),
            ];
            model('fenxiao_withdraw')->update($data, [ [ 'id', 'in', $ids ],['status','=',1] ]);
			
			model('fenxiao_withdraw')->commit();
            //提现成功发送消息
            foreach ($withdraw_list['data'] as $k => $v) {
                $message_model = new Message();
                $v['keywords'] = 'FENXIAO_WITHDRAWAL_SUCCESS';
                $message_model->sendMessage($v);
            }
			return $this->success();
		} catch (\Exception $e) {
			model('fenxiao_withdraw')->rollback();
			return $this->error('', $e->getMessage());
		}
	}
	
	/**
	 * 提现审核拒绝
	 * @param $id
	 * @return array
	 */
	public function withdrawRefuse($id)
	{
		$data = [
			'status' => -1,
			'payment_time' => time()
		];
		
		$info = model('fenxiao_withdraw')->getInfo([ [ 'id', '=', $id ] ], 'fenxiao_id,money,status');
		model('fenxiao_withdraw')->startTrans();
		try {
		    if($info['status'] == 1){
                model('fenxiao_withdraw')->update($data, [ [ 'id', '=', $id ] ]);
                //修改分销商提现中金额
                model('fenxiao')->setDec([ [ 'fenxiao_id', '=', $info['fenxiao_id'] ] ], 'account_withdraw_apply', $info['money']);
                model('fenxiao')->setInc([ [ 'fenxiao_id', '=', $info['fenxiao_id'] ] ], 'account', $info['money']);
                model('fenxiao_withdraw')->commit();
            }
			return $this->success();
		} catch (\Exception $e) {
			model('fenxiao_withdraw')->rollback();
			return $this->error('', $e->getMessage());
		}
		
	}
	
	/**
	 * 获取提现详情
	 * @param array $condition
	 * @return array
	 */
	public function getFenxiaoWithdrawInfo($condition = [], $field = '*')
	{
		$res = model('fenxiao_withdraw')->getInfo($condition, $field);
		return $this->success($res);
	}
	
	/**
	 * 获取分销列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getFenxiaoWithdrawList($condition = [], $field = '*', $order = '', $limit = null)
	{
		
		$list = model('fenxiao_withdraw')->getList($condition, $field, $order, '', '', '', $limit);
		return $this->success($list);
	}
	
	/**
	 * 获取分销提现分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getFenxiaoWithdrawPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$list = model('fenxiao_withdraw')->pageList($condition, $field, $order, $page, $page_size);
		return $this->success($list);
	}
    /**
     * 分销提现成功通知
     * @param $data
     */
    public function messageFenxiaoWithdrawalSuccess($data)
    {
        //发送短信
        $sms_model = new Sms();
        $var_parse = array(
            'fenxiaoname' => $data["fenxiao_name"],//会员名
            'money' => $data['money']
        );
        $data["sms_account"] = $data["mobile"];//手机号
        $data["var_parse"] = $var_parse;
        $sms_model->sendMessage($data);
        
        $member_model = new MemberModel();
        $member_info_result = $member_model->getMemberInfo([["member_id", "=", $data["member_id"]]]);
        $member_info = $member_info_result["data"];
        
        //绑定微信公众号才发送
        if (!empty($member_info) && !empty($member_info["wx_openid"])) {
        	$wechat_model = new WechatMessage();
        	$data["openid"] = $member_info["wx_openid"];
        	$data["template_data"] = [
        		'keyword1' => $data['money'],
        		'keyword2' => time_to_date($data['payment_time']),
        	];
        	$data["page"] = "";
        	$wechat_model->sendMessage($data);
        }
    }
    /**
     * 分销申请提现通知，卖家通知
     * @param $data
     */
    public function messageFenxiaoWithdrawalApply($data)
    {
        //发送短信
        $sms_model = new Sms();
        $var_parse = array(
            "fenxiaoname" => replaceSpecialChar($data["fenxiao_name"]),//会员名
            "money" => $data["money"],//退款申请金额
        );
//        $site_id    = $data['site_id'];
//        $shop_info  = model("shop")->getInfo([["site_id", "=", $site_id]], "mobile,email");
//        $message_data["sms_account"] = $shop_info["mobile"];//手机号
        $data["var_parse"] = $var_parse;
        $shop_accept_message_model = new ShopAcceptMessage();
        $result = $shop_accept_message_model->getShopAcceptMessageList();
        $list = $result['data'];
        if (!empty($list)) {
            foreach ($list as $v) {
                $message_data = $data;
                $message_data["sms_account"] = $v["mobile"];//手机号
                $sms_model->sendMessage($message_data);
                
                if($v['wx_openid'] != ''){               	
                	$wechat_model = new WechatMessage();
                	$data["openid"] = $v['wx_openid'];
                	$data["template_data"] = [
                			'keyword1' => replaceSpecialChar($data["fenxiao_name"]),
                			'keyword2' => time_to_date($data['create_time']),
                			'keyword3' => $data["money"]
                	];
                	$data["page"] = "";
                	$wechat_model->sendMessage($data);
                }
            }
        }
    }
	
}