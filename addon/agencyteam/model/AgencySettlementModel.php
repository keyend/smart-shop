<?php
namespace addon\agencyteam\model;
use addon\agencyteam\Model;
use app\model\system\Pay;
use app\model\system\Config as ConfigModel;

class AgencySettlementModel extends Model
{
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'agency_settlement';

    /**
     * 主键名称
     * @var string
     */
    protected $pk = 'id';

    /**
     * 结算方式
     *
     * @var array
     */
    private $types = [
        'bonus',
        'subsidy'
    ];

    /**
     * 结算名称
     *
     * @var array
     */
    private $typeTitles = [
        'bonus' => '区域分红',
        'subsidy' => '代理补贴'
    ];

    protected function __init()
    {
        $this->_model = model('agency_settlement');
    }

    /**
     * 返回名称列表
     *
     * @return void
     */
    public function getTypes($name = '')
    {
        if ($name == '') {
            return $this->typeTitles;
        } else {
            return $this->typeTitles[$name];
        }
    }

    /**
     * 返回名称
     *
     * @param integer $typeId
     * @return void
     */
    public function getType($typeId = null)
    {
        if ($typeId === null) {
            return $this->types;
        } else {
            return $this->types[$typeId];
        }
    }

    /**
     * 获取分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $condition[] = ['status', '=', 1];
        $query = self::where($condition);
        $count = $query->count();
        $query->field($field);
        $list = $query->limit($page_size)->page($page)->order('id DESC')->select()->toArray();
        $page_count = $page_size == 1 ? 1 : ceil($count / $page_size);

        return $this->success(compact('count', 'list', 'page_count'));
    }

    /**
     * 创建未结算记录
     *
     * @param integer $site_id
     * @return void
     */
    private function createNoneSignal($site_id = 0)
    {
        $res = [];

        foreach($this->types as $i => $key) {
            $lastTime = (int)self::where('status', 1)->where('type', $i)->value('last_time');
            $tradeNo = (new Pay())->createOutTradeNo();
            $row = [
                'site_id' => $site_id,
                'type' => $i,
                'begin_time' => $lastTime,
                'trade_no' => $tradeNo,
            ];
            $this->insert($row);
            $row['id'] = $this->getLastInsID();

            $res[] = $row;
        }

        return $res;
    }

    /**
     * 验证是否在结算周期
     *
     * @param integer $site_id
     * @return void
     */
    private function check($site_id = 0, $type = 'system.bot')
    {
        // 加入结算任务
        $config_info = app()->make(AgencyTeamConfigModel::class)->getSettlementConfig($data['site_id']);
        $config = $config_info['data']['value'];
        /**
         * 如果结算方式为自动结算
         * 添加队列到自动结算
         */
        if ($config['type'] == 0) {
            if ($type == 'system.bot') {
                // 启动自动结算，但是系统设置为手动结算
                return false;
            }
        } elseif($config['type'] == 1) {
            if ($type == 'manual') {
                // 系统设置为自动结算，但是进行手动结算
            }
        }

        // 结算时间
        $settlementTime = (int)$config['cycle_mix'] * 86400;
        $lastTime = (int)self::where('status', 1)->order('id DESC')->value('last_time');
        $lastTime += $settlementTime;
        $timestamp = time();

        // 最后结算时间 + 结算周期 < 当前时间 则可结算
        if ($lastTime > $timestamp) {
            return false;
        }

        return true;
    }

    /**
     * 获取未结算记录
     *
     * @param integer $site_id
     * @return void
     */
    public function getNoneSignal($site_id = 0)
    {
        $res = [];

        foreach($this->types as $i => $key) {
            $res[$key] = self::where('status', 0)->where('type', $i)->find();
            if (!$res[$key]) {
                $this->createNoneSignal($site_id);
                return $this->getNoneSignal();
            }
        }

        return $res;
    }

    /**
     * 订单结算
     *
     * @param int $site_id
     * @param string $opeartor 结算人
     * @param integer $timestamp 结算时间
     * @return void
     */
    public function settlement($site_id, $opeartor, $timestamp = 0)
    {
        // 结算周期验证
        if (!$this->check($site_id, $opeartor)) {
            return false;
        }

        $timestamp = $timestamp ? $timestamp : time();
        $signals = $this->getNoneSignal($site_id);
        $beginTime = $signals['bonus']['begin_time'];
        $settlement_users = [];
        $settlement_money = [];
        $settlement_ordercount = [];

        $orders = AgencyTeamOrderModel::where([
            ['is_bonus', '=', 0],
            ['is_subsidy', '=', 0],
            ['finish_time', 'BETWEEN', [$beginTime, $timestamp]],
        ])->select();

        try {
            foreach($orders as $order) {
                $response = $order->settlement($signals);

                $order->is_bonus = 1;
                $order->is_subsidy = 1;
                $order->settlement_money = $response['settlement_money'];
                $order->bonus_money = $response['bonus_money'];
                $order->subsidy_money = $response['subsidy_money'];
                $order->save();

                foreach($this->types as $type) {
                    if (!isset($settlement_users[$type])) {
                        $settlement_users[$type] = [];
                        $settlement_money[$type] = 0;
                        $settlement_ordercount[$type] = 0;
                    }

                    $settlement_users[$type] = array_merge($settlement_users[$type], $response['data']['members'][$type]);
                    $settlement_money[$type] += $response[$type . '_money'];

                    if ($response[$type . '_money'] > 0) {
                        $settlement_ordercount[$type] = $settlement_ordercount[$type] + 1;
                    }
                }
            }
        } catch(\Exception $e) {
            Log::error('结算失败:' . $e->getMessage());
        } finally {
            // 结算记录更新
            foreach($signals as $key => $signal) {
                $settlement_users[$key] = array_unique($settlement_users[$key]);
                // 写入结算记录
                $signal->user_total = count($settlement_users[$key]);
                $signal->order_count = $settlement_ordercount[$key];
                $signal->settlement_money = $settlement_money[$key];
                $signal->last_time = $timestamp;
                $signal->create_time = time();
                $signal->status = 1;
                $signal->operator = $opeartor;
                $signal->save();
            }
            // 用户月明细记录
            app()->make(AgencyAccountModel::class)->settlement($this->types[$signal['type']], $signal);
        }
    }
}