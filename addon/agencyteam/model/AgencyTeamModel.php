<?php
namespace addon\agencyteam\model;
use addon\agencyteam\Model;
use think\facade\Db;
use app\model\system\Config as ConfigModel;

class AgencyTeamModel extends Model
{
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'agency';

    /**
     * 主键名称
     * @var string
     */
    protected $pk = 'id';

    protected function __init()
    {
        $this->_model = model('agency');
    }

    /**
     * 模型关联
     * @return mixed
     */
    public function profile()
    {
        return $this->hasOne(AgencyMemberModel::class, 'member_id', 'member_id');
    }

    /**
     * 复位月明细
     *
     * @return void
     */
    public function resetMonthPerformance()
    {
        self::query("UPDATE " . config('database.connections.mysql.prefix') . "{$this->name} SET interim_performance = 0");
    }

    /**
     * 清除冗余数据
     *
     * @return void
     */
    public function clearRedundancy()
    {
        $query = new static();
        $ids = $query->alias('tm')->join('member um', 'tm.member_id = um.member_id', 'left')->whereExp("um.member_id", 'IS NULL')->value('GROUP_CONCAT(tm.id)');
        if (!empty($ids)) {
            $query->query("DELETE FROM " . config('database.connections.mysql.prefix') . "{$this->name} WHERE id IN ({$ids})");
        }
        app()->make(AgencyTeamMapsModel::class)->clearRedundancyMaps();
    }

    /**
     * 累加推荐人
     *
     * @param array $data
     * @return void
     */
    public function accumulation($member, $num = 1)
    {
        static $ids = [];

        if (!isset($member['parent_id'])) {
            $condition = [['member_id', '=', $member['member_id']]];
            $member = $this->where($condition)->field('id,parent_id,member_id,companion')->find();
        } else {
            $condition = [['member_id', '=', $member['parent_id']]];
            $member = $this->where($condition)->field('id,parent_id,member_id,companion')->find();
            if ($member) {
                $member->companion = $member->companion + $num;
                $member->save();
            }
        }

        if ($member) {
            $ids[] = $member['member_id'];

            if ($member->parent_id != 0) {
                $this->accumulation($member, $num);
            }
        }

        sort($ids);

        return app()->make(AgencyTeamMapsModel::class)->updateAgencyTeamRelationMaps($ids);
    }

    /**
     * 向上累加业绩
     *
     * @param integer $member_id
     * @return void
     */
    public function accumulationPerformance($member_id = 0, $quantity = 0, $parent_flag = 0)
    {
        $user_parent = $this->where('member_id', $member_id)->field('id,parent_id,member_id,companion')->find();
        if ($parent_flag) {
            $user_parent->performance = Db::raw('performance+' . $quantity);
            $user_parent->interim_performance = Db::raw('interim_performance+' . $quantity);
            $user_parent->save();
        }

        if ($user_parent->parent_id != 0) {
            $this->accumulationPerformance($user_parent->parent_id, $quantity, 1);
        }
    }

    /**
     * 获取团队明细
     *
     * @param int $id
     * @param string $fields
     * @return void
     */
    public function getInfo($id, $fields = '*')
    {
        $query = Db::name('member')->alias('um')->leftJoin($this->name . ' ag', 'um.member_id = ag.member_id');
        $query->field($field);
        $query->where('um.member_id', $id);
        $data = $query->find();

        return $data;
    }

    /**
     * 获取团队分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $query = Db::name('member')->alias('um')->leftJoin($this->name . ' ag', 'um.member_id = ag.member_id');
        $query->field($field);
        $query->where($condition);
        $query->when(empty($condition), function($query) {
            $query->whereRaw('((ag.level != 0) OR (ag.area_id != 0))');
        });
        $count = $query->count();
        $list = $query->limit($page_size)->page($page)->select()->toArray();
        $page_count = $page_size == 1 ? 1 : ceil($count / $page_size);
        $page_reload = false;

        foreach($list as $i => $row) {
            if ($row['username'] == '') {
                $list[$i]['username'] = $row['nickname'];
            }

            if ($row['id'] == null) {
                $page_reload = true;
                $register = new \addon\agencyteam\event\MemberRegister();
                $register->handleRegister([
                    'member_id' => $row['user_id'],
                    'site_id' => $row['member_siteid']
                ]);
            }
        }

        if ($page_reload) {
            return $this->getList($condition, $page, $page_size, $order, $field);
        }

        return $this->success(compact('count', 'list', 'page_count'));
    }

    /**
     * 返回团队明细
     *
     * @param array $agency
     * @param integer $page
     * @param integer $page_size
     * @return void
     */
    public function getTeamList($agency, $page = 1, $page_size = PAGE_LIST_ROWS)
    {
        $count = 0;
        $page_count = 0;
        $list = [];
        $maps = AgencyTeamMapsModel::where('member_id', $agency['member_id'])->value('maps');
        if ($maps != null) {
            $query = Db::name('member')->alias('um')->leftJoin($this->name . ' ag', 'um.member_id = ag.member_id');
            $fields = 'um.username,um.nickname,um.member_id as user_id,um.reg_time,um.headimg,ag.*';
            $query->field($field);
            $query->where('um.member_id', 'IN', $maps);
            $count = $query->count();
            $list = $query->limit($page_size)->page($page)->select()->toArray();
            $page_count = $page_size == 1 ? 1 : ceil($count / $page_size);

            foreach($list as $i => $row) {
                if ($row['username'] == '') {
                    $list[$i]['username'] = $row['nickname'];
                }
            }
        }

        return $this->success(compact('count', 'list', 'page_count'));
    }

    /**
     * 更新团队状态
     *
     * @param array $data
     * @return array
     */
    public function edit($data)
    {
        $origin = $this->where('id', (int)$data['id'])->find();
        if ($origin) {
            if ($origin['is_audit'] == 0) {
                if ($data['level'] != 0 || $data['area_id'] != 0) {
                    $data['is_audit'] = 1;
                    $data['status'] = 2;
                    $data['create_time'] = time();
                }
            }

            $this->update($data);
        }

        return $this->success();
    }

    /**
     * 指定ID排序
     *
     * @param array $data
     * @param array $sorts
     * @param string $key
     * @return void
     */
    private function array_sort_by_keys($data, $sorts, $key)
    {
        $result = [];

        foreach($sorts as $value) {
            foreach($data as $row) {
                if ($row[$key] == $value) {
                    $result[] = $row;
                }
            }
        }

        return $result;
    }

    /**
     * 返回补贴发放名单
     * 
     * 返回结构 ['id' => 120334, 'subsidy' => 0.0001]
     *
     * @param integer $member_id
     * @return void
     */
    public function getSubsidyMembers($member_id)
    {
        // 关系列表
        $members_ids = app()->make(AgencyTeamMapsModel::class)->getParents($member_id);
        if (!$members_ids) {
            return [];
        }

        // 补贴发放用户
        $result = [];
        $members_list = self::where([
            ['member_id', 'IN', $members_ids],
            ['status', '=', 2],
            ['level', '>', 0],
            ['is_audit', '=', 1]
        ])->select();

        if ($members_list) {
            $members = $this->array_sort_by_keys($members_list, $members_ids, 'member_id');
            $level_ids = array_unique(array_values(array_column($members, 'level')));
            $level_list = app()->make(AgencyTeamLevelModel::class)->getInfo($level_ids);
            $level_split = [];

            foreach($members as $member) {
                // if ($member['actives'] < $level['subsidy_condition']['usertotal']) {
                    // 有效用户数不达到
                // } elseif($member['performance'] < $level['subsidy_condition']['achievement']) {
                    // 业绩不达标
                // }
                if (!isset($level_split[$member['level']])) {
                    if (!isset($level_list[$member['level']])) {
                        continue;
                    }

                    $level_split[$member['level']] = 1;
                    $level = $level_list[$member['level']];

                    $result[] = [
                        'id' => $member['member_id'],
                        'subsidy' => round($level['rate_subsidy'] / 100, 4),
                        'samepeer' => false
                    ];
                } elseif($level_split[$member['level']] == 1) {
                    $level_split[$member['level']] = 2;
                    $level = $level_list[$member['level']];

                    if ($level['subsidy_condition']['samepeer'] > 0) {
                        $result[] = [
                            'id' => $member['member_id'],
                            'subsidy' => round($level['subsidy_condition']['samepeer'] / 100, 4),
                            'samepeer' => true
                        ];
                    }
                }
            }
        }

        return $result;
    }

    /**
     * 返回对应区域
     *
     * @param array $maps
     * @return void
     */
    private function getRegionalMemberCondition($maps)
    {
        static $condition = [
            ['status', '=', 2],
            ['is_audit', '=', 1],
            ['area_id', '<>', 0]
        ];

        $data = $this->where(array_merge($condition, $maps))->find();
        if ($data) {
            $area_info = AgencyTeamAreaModel::where('id', $data['area_id'])->find();

            return [
                'id' => $data['member_id'],
                'bonus' => round($area_info['rate_bonus'] / 100, 4)
            ];
        }
    }

    /**
     * 返回区域代理列表
     * 
     * ['id' => 123412, 'bonus' => 0.01]
     *
     * @param array $order
     * @return void
     */
    public function getRegionalMembers($order)
    {
        $res = [];

        // 县级经理
        if ($order['district_id'] != 0) {
            $district = $this->getRegionalMemberCondition([['district_id', '=', (int)$order['district_id']]]);
            if ($district) {
                $res[] = $district;
            }
        }

        // 市级经理
        if ($order['city_id'] != 0) {
            $city = $this->getRegionalMemberCondition([
                ['city_id', '=', (int)$order['city_id']],
                ['district_id', '=', 0]
            ]);
            if ($city) {
                $res[] = $city;
            }
        }

        // 省级经理
        if ($order['province_id'] != 0) {
            $province = $this->getRegionalMemberCondition([
                ['province_id', '=', (int)$order['province_id']],
                ['city_id', '=', 0],
                ['district_id', '=', 0]
            ]);
            if ($province) {
                $res[] = $province;
            }
        }

        return $res;
    }

    /**
     * 自动升级团队
     *
     * @param integer $level_id
     * @return void
     */
    public function auto_upgrade_team_level($level_id = 0)
    {
        $level_list = app()->make(AgencyTeamLevelModel::class)->getLevel();
        if (empty($level_list)) {
            throw new \Exception('团队代理等级获取失败!');
        }

        $level_data = $level_list[0];
        $condition = $level_data['condition'];
        if ($level_data['level'] <= $this->getAttr('level')) {
            // 已升级
            return false;
        }

        if ($level_data['upgrade'] == 0) {
            // 未启用自动升级
            return false;
        }

        // 源会员数据
        $profile = $this->getAttr('profile');
        $timestamp = time();
        // 注册之日起
        if ($condition['action'] == 1) {
            $times = $condition['days'] * 86400;
            $expiretime = intval($profile['reg_time']) + $times;
            if ($expiretime < $timestamp) {
                // 已超出时间
                return false;
            }

            // 直接推荐人数
            $recommentCount = AgencyMemberModel::where('source_member', $this->getAttr('member_id'))->where('member_level', '>=', $level_id)->count();
            if ($recommentCount < $condition['people']) {
                // 推荐人数不够
                return false;
            }
        }

        // 会员升级
        $this->setAttr('level', $level_data['level']);
        $this->setAttr('levelname', $level_data['levelname']);
        $this->setAttr('status', 2);
        $this->setAttr('is_audit', 1);
        $this->setAttr('create_time', $timestamp);
        $this->save();
    }

    /**
     * 设置团队状态
     *
     * @param integer $member_id
     * @param integer $status
     * @return void
     */
    public function setStatus($member_id = 0, $status = 0)
    {
        $member = self::where('member_id', $member_id)->find();
        if (!$member) {
            return $this->error('团队不存在');
        }
        $member->status = $status;
        $member->save();

        return $this->success();
    }
}