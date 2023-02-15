<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
define('IN_MOBILE', true);
require '../../../framework/bootstrap.inc.php';
require '../../../app/common/bootstrap.app.inc.php';
require './request.php';

load()->app('common');

global $_W;
global $_GPC;

// 配置回调URL
if (!isset($_GPC['method']) || $_GPC['method'] === '') {
    $_GPC['method'] = 'callback';
}
// 默认配置
$_W['uniacid'] = '1';

$req = new unionPayRequest();
$uniacid = $_W['uniacid'];
try {
    $response = $req->index(array(
        'method' => $_GPC['method']
    ));
    $params = $response->getAllParameters();

    // 订单号
    $orderId = $params['out_trade_no'];
    $tags = [];
    // 订单金额
    $tags['amount'] = floatval($params['total_fee']);
    // 实际支付
    $tags['cash'] = floatval($params['cash_fee']);
    // 现金券金额
    $tags['coupon'] = floatval($response->getParameter('coupon_fee'));
    // 免充值优惠金额
    $tags['mdiscount'] = floatval($response->getParameter('mdiscount'));
    // 三方订单号
    $tags['out_transaction_id'] = $params['out_transaction_id'];
    // 平台交易号
    $tags['transaction_id'] = $params['transaction_id'];
    // 支付卡号
    $cardId = $response->getParameter('bank_billno');
    $bankType = $response->getParameter('bank_type');
    $cardType = 0;
    // 是否为卡支付
    $isCard = $cardId === '' ? 1 : 0;

    $method = $params['method'];
    if ($method === 'recharge') {
        // 充值历史
        $log = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member_log') . ' WHERE `uniacid`=:uniacid and `logno`=:logno limit 1', array(':uniacid' => $uniacid, ':logno' => $orderId));
        $log['money'] = floatval($log['money']);
        $cashAmount = round($tags['cash'] / 100, 2);
        if ($cashAmount >= $log['money']) {
            // pass
        } else {
            pdo_update('ewei_shop_member_log', array(
                'status' => -2,
                'sendmoney' => $tags['cash'],
                'senddata' => json_encode($params)
            ), array(
                'id' => $log['id']
            ));
            Utils::dataRecodes('RECHARGE ERROR','支付金额不正确' . $log['money'] . ":" . $tags['cash']);
            die('failure');
        }

        require_once IA_ROOT . '/addons/ewei_shopv2/version.php';
        require_once IA_ROOT . '/addons/ewei_shopv2/defines.php';
        require_once EWEI_SHOPV2_INC . 'functions.php';

        if (!empty($log) && $log['status'] == '0') {
            // 更新支付历史状态
            pdo_update('ewei_shop_member_log', array(
                'status' => 1,
                'rechargetype' => 'unionpay',
                'apppay' => 0,
                'transid' => $tags['transaction_id'],
                'sendmoney' => $tags['cash'],
                'senddata' => json_encode($params)
            ), array(
                'id' => $log['id']
            ));

            // $shopset = m('common')->getSysset('shop');

            m('member')->setCredit($log['openid'], 'credit2', $log['money'], array(1, '会员充值 在线支付', $log['logno']));
			m('member')->setRechargeCredit($log['openid'], $log['money']);

            com_run('sale::setRechargeActivity', $log);
			com_run('coupon::useRechargeCoupon', $log);

            m('notice')->sendMemberLogMessage($log['id']);

            $member = m('member')->getMember($log['openid']);
			$params = array('nickname' => empty($member['nickname']) ? '未更新' : $member['nickname'], 'price' => $log['money'], 'paytype' => '银联支付', 'paytime' => date('Y-m-d H:i:s', time()));

            com_run('printer::sendRechargeMessage', $params);
        }
    } else {
        // 支付历史
        $log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(':uniacid' => $uniacid, ':module' => 'ewei_shopv2', ':tid' => $orderId));
        if (!empty($log) && $log['status'] == '0') {
            $log['fee'] = floatval($log['fee']);
            $cashAmount = round($tags['cash'] / 100, 2);
            if ($cashAmount >= $log['fee']) {
                // pass
            } else {
                Utils::dataRecodes('PAYMENT ERROR','支付金额不正确' . $log['fee'] . ":" . $tags['cash']);
                die('failure');
            }

            if ($tags['coupon'] > 0) {
                $cardType = 1;
            }
            // 使用现金券金额
            if ($log['is_usecard'] == 1 && $cardType == 1 && !empty($log['encrypt_code']) && $log['acid']) {
                load()->classs('coupon');
                $acc = new coupon($log['acid']);
                $codearr['encrypt_code'] = $log['encrypt_code'];
                $codearr['module'] = $log['module'];
                $codearr['card_id'] = $log['card_id'];
                $acc->PayConsumeCode($codearr);
            }

            if ($tags['mdiscount'] > 0) {
                $cardType = 2;
            }
            // 免充值优惠金额
            if ($log['is_usecard'] == 1 && $cardType == 2) {
                $now = time();
                $log['card_id'] = intval($log['card_id']);
                $iscard = pdo_fetchcolumn('SELECT iscard FROM ' . tablename('modules') . ' WHERE name = :name', array(':name' => $log['module']));
                $condition = '';

                if ($iscard == 1) {
                    $condition = ' AND grantmodule = \'' . $log['module'] . '\'';
                }

                pdo_query('UPDATE ' . tablename('activity_coupon_record') . (' SET status = 2, usetime = ' . $now . ', usemodule = \'' . $log['module'] . '\' WHERE uniacid = :aid AND couponid = :cid AND uid = :uid AND status = 1 ' . $condition . ' LIMIT 1'), array(':aid' => $_W['uniacid'], ':uid' => $log['openid'], ':cid' => $log['card_id']));
            }
            // 更新支付历史状态
            pdo_update('core_paylog', array(
                'tag' => serialize($tags),
                'status' => 1,
                'uniontid' => $params['transaction_id'],
                'is_usecard' => $isCard,
                'card_id' => $cardId,
                'card_type' => $cardType,
                'card_fee' => $tags['cash']
            ), array(
                'tid' => $log['tid']
            ));

            $site = WeUtility::createModuleSite($log['module']);

            if (!is_error($site)) {
                $method = 'payResult';

                if (method_exists($site, $method)) {
                    $ret = array();
                    $ret['weid'] = $log['uniacid'];
                    $ret['uniacid'] = $log['uniacid'];
                    $ret['result'] = 'success';
                    $ret['type'] = $log['type'];
                    $ret['from'] = 'return';
                    $ret['tid'] = $log['tid'];
                    $ret['user'] = $log['openid'];
                    $ret['fee'] = $log['fee'];
                    $ret['tag'] = $log['tag'];
                    $ret['is_usecard'] = $log['is_usecard'];
                    $ret['card_type'] = $log['card_type'];
                    $ret['card_fee'] = $log['card_fee'];
                    $ret['card_id'] = $log['card_id'];
                    $site->{$method}($ret);
                }
            }
        }
    }

    echo('success');
} catch(\Exception $e) {
    Utils::dataRecodes('ERROR','接口回调错误:' . $e->getMessage());
    echo('failure');
}
