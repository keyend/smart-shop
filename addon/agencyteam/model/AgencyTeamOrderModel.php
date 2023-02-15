<?php
namespace addon\agencyteam\model;
use addon\agencyteam\Model;
use app\model\system\Config as ConfigModel;
use app\model\member\MemberAccount;
use think\facade\Db;

class AgencyTeamOrderModel extends Model
{
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'agency_order';

    /**
     * 主键名称
     * @var string
     */
    protected $pk = 'id';

    // 当前时间
    protected $timestamp;

    protected function __init()
    {
        $this->timestamp = time();
    }

    /**
     * 获取订单明细
     *
     * @param integer $order_id
     * @return void
     */
    public function getInfo($order_id = 0)
    {
        $order_exist = $this->where('order_id', $order_id)->count();
        if ($order_exist) {
            // 订单已存在
            throw new \Exception("团队订单【{$order_id}】已存在!");
        }

        $order_info = model('order')->getFirstData([['order_id', '=', $order_id]]);
        if (!$order_info) {
            throw new \Exception("团队订单【{$order_id}】不存在!");
        }

        // 验证订单商品是否参与活动
        $order_info['promotion_addon'] = false;
        if ($order_info['promotion_type'] != '') {
            $order_info['promotion_addon'] = true;
        }

        return $order_info;
    }

    /**
     * 添加新的结算订单
     *
     * @param array $data
     * @return void
     */
    public function add($data = [])
    {
        $user_model = app()->make(AgencyTeamModel::class);
        // 获取补贴发放名单
        $distribution = $user_model->getSubsidyMembers($data['member_id']);
        // 区域分红名单
        $regional = $user_model->getRegionalMembers($data);
        // 结算信息
        $signals = app()->make(AgencySettlementModel::class)->getNoneSignal($data['site_id']);

        $data['subsidy'] = $distribution;
        $data['bonus'] = $regional;

        // 金额计算
        $calculate = $this->calculate($data);
        list($subsidy_money, $bonus_money) = $calculate;

        $data['order_address'] = $data['full_address'];
        $data['settlement_money'] = array_sum($calculate);
        $data['subsidy_money'] = $subsidy_money;
        $data['bonus_money'] = $bonus_money;
        $data['subsidy'] = json_encode($data['subsidy']);
        $data['bonus'] = json_encode($data['bonus']);
        $data['bonus_settlement_id'] = $signals['bonus']['id'];
        $data['subsidy_settlement_id'] = $signals['subsidy']['id'];

        // 如果有存在需要结算
        if ($data['settlement_money'] > 0) {
            // 添加订单
            $this->insert($data);
            // 累加业绩
            $user_model->accumulationPerformance($data['member_id'], $data['order_money']);

            foreach($signals as $key => $signal) {
                if (($key == 'bonus' && $bonus_money) > 0) {
                    $signal->order_money = Db::raw('order_money+' . $data['order_money']);
                    $signal->order_count = Db::raw('order_count+1');
                    $signal->settlement_money = Db::raw('settlement_money+' . $bonus_money);
                    $signal->save();
                } elseif($key == 'subsidy' && $subsidy_money > 0) {
                    $signal->order_money = Db::raw('order_money+' . $data['order_money']);
                    $signal->order_count = Db::raw('order_count+1');
                    $signal->settlement_money = Db::raw('settlement_money+' . $subsidy_money);
                    $signal->save();
                }
            }
        }
    }

    /**
     * 计算结算分配金额
     *
     * @param array $data
     * @return void
     */
    private function calculate($data)
    {
        $subsidy_money = 0;
        $bonus_money = 0;
        $order_money = (float)$data['order_money'];

        foreach($data['subsidy'] as $subsidy) {
            $subsidy_money += $subsidy['subsidy'] * $order_money;
        }

        foreach($data['bonus'] as $bonus) {
            $bonus_money += $bonus['bonus'] * $order_money;
        }

        return [$subsidy_money, $bonus_money];
    }

    /**
     * 返回最后结算时间
     *
     * @return int
     */
    private function getLastSettlementTime()
    {
        return (int)AgencySettlementModel::where('status', 1)->order('id DESC')->value('last_time');
    }

    /**
     * 添加结算
     *
     * @return void
     */
    public function getSettlementTaskTime($data)
    {
        // 加入结算任务
        $config_info = app()->make(AgencyTeamConfigModel::class)->getSettlementConfig($data['site_id']);
        $config = $config_info['data']['value'];
        /**
         * 如果结算方式为自动结算
         * 添加队列到自动结算
         */
        if ($config['type'] == 0) {
            throw new \Exception('使用手动结算!');
        }

        // 验证是否已存在预约
        $settlementTime = redis()->get('appointment_settlement_ww_b16');
        if ($settlementTime != null) {
            throw new \Exception('已存在预约!');
        }

        // 结算时间
        $settlementTime = (int)$config['cycle_mix'] * 86400;
        // 上次结算
        $lastSettlementTime = $this->getLastSettlementTime();
        // 没有结算过(必需进行一次手动结算)
        if ($lastSettlementTime == 0) {
            // throw new \Exception('暂无结算!');
            $lastSettlementTime = time();
        }

        $settlementTime = $settlementTime + $lastSettlementTime;
        redis()->set('appointment_settlement_ww_b16', $settlementTime);
        $timestamp = time();
        $resultTime = $settlementTime - $timestamp;

        return $resultTime;
    }

    /**
     * 分账
     *
     * @param string $name
     * @param array $signal
     * @param array $data
     * @return void
     */
    private function split($name, $signal, &$data)
    {
        static $config;
        // 应用账户
        $useAccounts = ['balance'];
        // 标题
        $titles = [
            'bonus' => '区域分红',
            'subsidy' => '代理补贴'
        ];
        // 分红总额
        $money = 0;
        // 分红分配
        $items = json_decode($data[$name], true);
        // 用户账户
        $member_account_model = app()->make(MemberAccount::class);
        // 团队账户
        $agency_account_model = app()->make(AgencyAccountModel::class);
        // 更新用户账户
        if ($config == null) {
            $config_info = app()->make(AgencyTeamConfigModel::class)->getSettlementConfig($data['site_id']);
            $config = $config_info['data']['value'];
        }

        if ($items != null) {
            foreach($items as $item) {
                // 账户列表
                $accounts = $agency_account_model->getAccounts($item['id']);
                // 新增金额
                $value = round($item[$name] * $data['order_money'], 2);
                // 加入用户列表
                $data['members'][$name][] = $item['id'];
                // 累加金额
                $money += $value;

                if ($value) {
                    foreach($accounts as $account) {
                        if (in_array($account->name, $useAccounts)) {
                            // 验证如果用户已冻结
                            if ($account->member->status == 0) {
                                break;
                            }
                            // 更新账目
                            $account->value = Db::raw('value+'.$value);
                            $account->save();
                            // 记录团队结算账目
                            $agency_account_model->addAccount($name, $item['id'], $account->name, $value, $data, $signal);
                            if ($config['add_balance'] == 1) {
                                $member_account_model->addMemberAccount($data['site_id'], $account["member_id"], $account->name, $value, "settlement", "团队结算", "团队订单[{$data['order_no']}]结算{$titles[$name]}");
                            }
                        } elseif($account->name == $name) {
                            // 更新账目
                            $account->value = Db::raw('value+'.$value);
                            $account->save();
                        }
                    }
                }
            }
        }

        return $money;
    }

    /**
     * 团队订单结算
     *
     * @param array $data
     * @return array
     */
    public function settlement($signals)
    {
        $data = $this->getData();
        $data['order_money'] = (float)$data['order_money'];
        // 关系用户
        $data['members'] = [];
        // 结算总额
        $settlement_money = 0;
        // 补贴总额
        $subsidy_money = 0;
        // 分红总额
        $bonus_money = 0;

        // 补贴结算
        foreach($signals as $key => $signal) {
            $data['members'][$key] = [];
            $name = "{$key}_money";
            $$name = $this->split($key, $signal, $data);
        }

        $settlement_money = $bonus_money + $subsidy_money;

        return compact('data', 'settlement_money', 'bonus_money', 'subsidy_money');
    }

    /**
     * 返回订单列表记录
     *
     * @param array $condition
     * @param integer $index
     * @param integer $limit
     * @return void
     */
    public function getOrderPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'ao.order_id DESC')
    {
        $field = '
        ao.order_id,ao.member_id,ao.out_trade_no,ao.order_no,ao.order_type,ao.order_status,ao.order_money,ao.order_address,
        ao.settlement_money,ao.bonus_money,ao.subsidy_money,ao.finish_time,ao.subsidy,ao.bonus,ao.is_bonus,ao.is_subsidy,
        o.order_status_name,o.create_time
        ';
        $alias = 'ao';
        $join = [
            [
                'order o',
                'ao.order_id = o.order_id',
                'inner'
            ]
        ];
        $list = model($this->name)->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
        if (!empty($list['list'])) {
            foreach ($list['list'] as $k => $item) {
                $item['is_bonus'] = (int)$item['is_bonus'];
                $item['is_subsidy'] = (int)$item['is_subsidy'];
                $item['order_money'] = (float)$item['order_money'];
                $order_info = model('order')->getInfo([['order_id', '=', $item['order_id']]], 'name,full_address,mobile,order_status_name');
                $list['list'][$k] = array_merge($list['list'][$k], $order_info);
                $order_goods = model('order_goods')->getList([['order_id', '=', $item['order_id']]]);
                $list['list'][$k]['order_goods'] = $order_goods;
                $list['list'][$k]['is_settlement'] = $item['is_bonus'] && $item['is_subsidy'];
                $list['list'][$k]['subsidy'] = $this->parseSettlementInfo($item, 'subsidy');
                $list['list'][$k]['bonus'] = $this->parseSettlementInfo($item, 'bonus');
            }
        }

        return $this->success($list);
    }

    /**
     * 解析预结算信息
     *
     * @param array $data
     * @param string $field
     * @return void
     */
    private function parseSettlementInfo($data = [], $field = '')
    {
        $result = [];
        $rows = json_decode($data[$field], true);
        if ($rows == null) {
            return [];
        }

        foreach($rows as $i => $row) {
            $agency_info = model('agency')->getInfo([['member_id', '=', $row['id']]], 'level,levelname');
            if ($agency_info['level'] != 0) {
                $member_info = model('member')->getInfo([['member_id', '=', $row['id']]], 'nickname');
                $row['nickname'] = $member_info['nickname'] . "({$agency_info['levelname']})";
                $row[$field] = (float)$row[$field];
                $row[$field] = number_format($row[$field] * $data['order_money'], 2, '.', '');
                $result[] = $row;
            }
        }

        return $result;
    }
}