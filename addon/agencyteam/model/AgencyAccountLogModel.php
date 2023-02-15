<?php
namespace addon\agencyteam\model;
use addon\agencyteam\Model;
use app\model\system\Config as ConfigModel;

class AgencyAccountLogModel extends Model
{
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'agency_account_logs';

    /**
     * 主键名称
     * @var string
     */
    protected $pk = 'id';

    /**
     * 导出字段预定义
     *
     * @var array
     */
    protected $fields_maps = [
        'trade_no' => '流水号',
        'order_no' => '订单编号',
        'buyer_id' => '购买人ID',
        'buyer' => '购买人',
        'order_address' => '收货地址',
        'order_money' => '订单金额',
        'finish_time' => '订单完成',
        'account_type' => '所属账户',
        'settlement_type' => '所属财务',
        'value' => '获得津贴',
        'member_id' => '用户ID',
        'username' => '用户名',
        'nickname' => '呢称',
        'mobile' => '联系方式',
        'create_time' => '结算时间',
    ];

    /**
     * 获取用户列表
     *
     * @param array $ids
     * @return void
     */
    private function getMemberList($ids = [])
    {
        $member_list = model('member')->getModel()->where('member_id', 'IN', $ids)->field('member_id,username,nickname')->select()->toArray();
        $members = [];
        foreach($member_list as $member) {
            $members[$member['member_id']] = $member;
        }

        return $members;
    }

    /**
     * 返回统计结果
     *
     * @param integer $tid
     * @param array $condition
     * @return void
     */
    private function getComplexTotal($tid = 0, $condition = [])
    {
        $query = new self;
        $settlement_type = app()->make(AgencySettlementModel::class)->getType($tid);
        $condition[] = ['settlement_type', '=', $settlement_type];
        $value = (float)$query->where($condition)->sum('value');

        return $value;
    }

    /**
     * 导出数据
     *
     * @param array $data
     * @return void
     */
    private function export($data = [])
    {
        static $readyState, $filename, $filepath, $savepath, $orders = [], $users = [];

        $content = '';
        if ($readyState == null) {
            $readyState = 1;
            $filename = date('Ymd_His', time()) . '.csv';
            $filepath = 'upload/agencyteam_csv/';
            $savepath = $filepath . $filename;

            if (dir_mkdir($filepath)) {
                $headers = array_values($this->fields_maps);
                $headers = implode('","', $headers);
                $content = iconv('UTF-8', 'GBK//TRANSLIT', "\"{$headers}\"\n");
            }
        } elseif(empty($data)) {
            header('Content-type:application/octet-stream');
            header('Content-Disposition:attachment;filename = ' . $filename);
            header('Accept-ranges:bytes');
            header('Accept-length:' . filesize($savepath));
            readfile($savepath);
            @unlink($savepath);
            exit();
        }
        // 加入订单数据
        $order_model = app()->make(AgencyTeamOrderModel::class);
        if (!isset($orders[$data['order_no']])) {
            $orders[$data['order_no']] = $order_model->where('order_no', $data['order_no'])->field('member_id,order_id,order_money,order_type,finish_time')->find();
            if ($orders[$data['order_no']]) {
                $orders[$data['order_no']] = $orders[$data['order_no']]->toArray();
            }
        }
        // 加入买家数据
        $user_model = app()->make(AgencyMemberModel::class);
        if (!isset($users[$orders[$data['order_no']]['member_id']])) {
            $users[$orders[$data['order_no']]['member_id']] = $user_model->where('member_id', $orders[$data['order_no']]['member_id'])->field('username,nickname,mobile')->find();
            if ($users[$orders[$data['order_no']]['member_id']]) {
                $users[$orders[$data['order_no']]['member_id']] = $users[$orders[$data['order_no']]['member_id']]->toArray();
            }
        }
        // 买家明细
        $buyer = [
            'buyer' => $users[$orders[$data['order_no']]['member_id']]['username'],
            'buyer_id' => $orders[$data['order_no']]['member_id'],
        ];
        // 收款人信息
        if (!isset($users[$data['member_id']])) {
            $users[$data['member_id']] = $user_model->where('member_id', $data['member_id'])->field('username,nickname,mobile')->find();
            if ($users[$data['member_id']]) {
                $users[$data['member_id']] = $users[$data['member_id']]->toArray();
            }
        }
        // 收款人信息
        $user_data = $users[$data['member_id']];
        $cells = [];
        $data = array_merge($data->toArray(), $orders[$data['order_no']], $buyer, $user_data);
        $settlement_model = app()->make(AgencySettlementModel::class);
        $account_model = app()->make(AgencyAccountModel::class);

        foreach($this->fields_maps as $key => $value) {
            if (strpos($key, '_time') !== false) {
                if (is_numeric($data[$key])) {
                    $data[$key] = date('Y/m/d H:i:s', $data[$key]);
                }
            } elseif (strpos($key, '_no') !== false) {
                $data[$key] = "\t" . $data[$key];
            } elseif ($key == 'mobile') {
                $data[$key] = "\t" . $data[$key];
            } elseif ($key == 'account_type') {
                $data[$key] = $account_model->getTypes($data[$key]);
            } elseif ($key == 'settlement_type') {
                $data[$key] = $settlement_model->getTypes($data[$key]);
            }

            if (isset($data[$key])) {
                $cells[] = str_replace('"', '""', $data[$key]);
            } else {
                $cells[] = '--';
            }
        }
        $content .= '"' . iconv('UTF-8', 'GBK//TRANSLIT', implode('","', $cells)) . "\"\n";
        // 写入临时文件
        file_put_contents($savepath, $content, 8);
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
        $query = self::where($condition);
        $count = $query->count();
        $bonus = $this->getComplexTotal(0, $condition);
        $subsidy = $this->getComplexTotal(1, $condition);
        $list = [];

        if ($page > 0) {
            $query->field($field);
            if ($page_size == 9998) {
                if ($count <= 0) {
                    throw new \Exception('导出记录为空!');
                }
                set_time_limit(0);
                $query->chunk(1000, function($list) {
                    foreach($list as $row) {
                        $this->export($row);
                    }
                });
                return $this->export();
            } else {
                $list = $query->limit($page_size)->page($page)->select()->toArray();
                $page_count = $page_size == 1 ? 1 : ceil($count / $page_size);
                $sql = $query->getLastSql();
                $types = app()->make(AgencySettlementModel::class)->getTypes();
                $accountType = ['balance' => '账户余额', 'point' => '积分', 'growth' => '成长值'];
                $memberIds = array_values(array_column($list, 'member_id'));
                $memberList = $this->getMemberList($memberIds);
                array_walk($list, function(&$item) use($types, $accountType, $memberList) {
                    $item['settlement_title'] = $types[$item['settlement_type']];
                    $item['account_title'] = $accountType[$item['account_type']];
                    $member = $memberList[$item['member_id']];
                    $item['accept_user'] = $member['username']!=''?$member['username']:$member['nickname'];
                    return $item;
                });
            }
        }

        return $this->success(compact('count', 'bonus', 'subsidy', 'list', 'page_count', 'sql'));
    }
}
