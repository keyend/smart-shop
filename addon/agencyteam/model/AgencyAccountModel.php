<?php
namespace addon\agencyteam\model;
use addon\agencyteam\Model;
use app\model\system\Config as ConfigModel;

class AgencyAccountModel extends Model
{
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'agency_account';

    /**
     * 主键名称
     * @var string
     */
    protected $pk = 'id';

    /**
     * 记录的账户
     *
     * @var array
     */
    protected $accounts = [
        'balance',  // 余额
        'point',    // 积分
        'growth',   // 成长值
        'bonus',    // 分红
        'subsidy'   // 补贴
    ];

    /**
     * 账户名称
     *
     * @var array
     */
    protected $accountTypes = [
        'balance' => '余额',
        'point' => '积分',
        'growth' => '成长值',
        'bonus' => '分红',
        'subsidy' => '补贴'
    ];

    /**
     * 账户缓存
     *
     * @var array
     */
    protected $storage = [];

    protected function __init()
    {
        $this->_model = model('agency_account');
    }

    /**
     * 模型关联
     * @return mixed
     */
    public function member()
    {
        return $this->hasOne(AgencyTeamModel::class, 'member_id', 'member_id');
    }

    /**
     * 返回账户信息
     *
     * @param string $name
     * @return void
     */
    public function getTypes($name = '')
    {
        if ($name == '') {
            return $this->accountTypes;
        } else {
            return $this->accountTypes[$name];
        }
    }

    /**
     * 返回账户列表
     *
     * @param integer $member_id
     * @param array $types
     * @return void
     */
    public function getAccounts($member_id = 0, $types = [])
    {
        $types = empty($types) ? $this->accounts : $types;
        $query = new static;
        $account_list = $query->where('member_id', $member_id)->with(['member'])->where('name', 'IN', $types)->select();
        if (empty($account_list->jsonSerialize())) {
            foreach($types as $type) {
                self::create([
                    'member_id' => $member_id,
                    'name' => $type,
                    'value' => 0
                ]);
            }

            return $this->getAccounts($member_id, $types);
        }

        return $account_list;
    }

    /**
     * 返回用户账户列表
     *
     * @param integer $member_id
     * @return void
     */
    public function getTeamSettlementAccount($member_id)
    {
        $account_types = app()->make(AgencySettlementModel::class)->getTypes();
        $account_list = [];

        foreach($this->getAccounts($member_id) as $account) {
            if (isset($account_types[$account['name']])) {
                $account_item = $account->toArray();
                $account_item['title'] = $account_types[$account['name']];
                $account_list[] = $account_item;
            }
        }

        return $account_list;
    }

    /**
     * 添加结算账目
     *
     * @param string $type          结算类型
     * @param integer $member_id    用户ID
     * @param string $account_type  所属账户
     * @param integer $amount       数值
     * @param array $order          订单明细
     * @param array $signal         结算明细
     * @return void
     */
    public function addAccount($type='', $member_id = 0, $account_type = '', $amount = 0, $order = [], $signal = [])
    {
        static $timestamp;
        if ($timestamp == null) {
            $timestamp = time();
        }
        // 添加财务记录
        AgencyAccountLogModel::create([
            'trade_no' => $signal['trade_no'],
            'order_no' => $order['order_no'],
            'member_id' => $member_id,
            'account_type' => $account_type,
            'settlement_type' => $type,
            'order_address' => $order['order_address'],
            'value' => $amount,
            'create_time' => $timestamp
        ]);
        // 增加值
        $this->setAccount($member_id, $account_type, $amount);
    }

    /**
     * 增加临时账户值
     *
     * @param integer $member_id    用户ID
     * @param string $name
     * @param integer $value
     * @return void
     */
    public function setAccount($member_id, $name = '', $value = 0)
    {
        // 累加缓存
        if (!isset($this->storage[$member_id])) {
            $account = [];
            foreach($this->accounts as $accountName) {
                $account[$accountName] = 0;
            }

            $this->storage[$member_id] = $account;
        }

        $this->storage[$member_id][$name] += floatval($value);
    }

    /**
     * 操作账户统一结算
     *
     * @param string $type_name
     * @param collection $signal
     * @return void
     */
    public function settlement($type_name = '', $signal)
    {
        $settlement_account_model = app()->make(AgencySettlementAccountModel::class);
        $settlement_team_model = app()->make(AgencyTeamModel::class);
        $settlement_account_name = 'balance';

        foreach($this->storage as $member_id => $values) {
            if (isset($values[$settlement_account_name]) && $values[$settlement_account_name] > 0) {
                if ($interim_performance == null) {
                    $interim_performance = (float)$settlement_team_model->where('member_id', $member_id)->value('interim_performance');
                }

                $settlement_account_model->insert([
                    'site_id' => $signal['site_id'],
                    'type' => $signal['type'],
                    'type_name' => $type_name,
                    'member_id' => $member_id,
                    'begin_time' => $signal['begin_time'],
                    'last_time' => $signal['last_time'],
                    'performance' => $interim_performance,
                    'value' => $values[$settlement_account_name]
                ]);
            }
        }

        app()->make(AgencyTeamModel::class)->resetMonthPerformance();
    }
}