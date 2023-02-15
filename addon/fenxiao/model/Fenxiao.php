<?php
namespace addon\fenxiao\model;
use app\model\BaseModel;
use app\model\member\Member;
use function GuzzleHttp\Psr7\str;
use think\facade\Db;
/**
 * 分销
 */
class Fenxiao extends BaseModel
{
    public $fenxiao_status_zh = [
        1 => '正常',
        -1 => '冻结',
    ];
    /**
     * 添加分销商
     * @param $data
     * @return mixed
     */
    public function addFenxiao($data)
    {
        $fenxiao_info = model('fenxiao')->getInfo(
            [['member_id', '=', $data['member_id']],['is_delete','=',0]],
            'fenxiao_id'
        );
        if (!empty($fenxiao_info)) return $this->error('', '已经是分销商了');
        $data['fenxiao_no'] = date('YmdHi') . rand(1000, 9999);
        $data['create_time'] = time();
        $data['audit_time'] = time();
        model('fenxiao')->startTrans();
        try {
            if (!empty($data['parent'])) {
                //添加上级分销商一级下线人数
                model('fenxiao')->setInc([['fenxiao_id', '=', $data['parent']],['is_delete','=',0]], 'one_child_fenxiao_num');
                //获取上上级分销商id
                $grand_parent_id = model('fenxiao')->getInfo([['fenxiao_id', '=', $data['parent']],['is_delete','=',0]], 'parent');
                if (!empty($grand_parent_id) || $grand_parent_id['parent'] != 0) {
                    //添加上上级分销商二级下线人数
                    model('fenxiao')->setInc([['fenxiao_id', '=', $grand_parent_id['parent']]], 'two_child_fenxiao_num');
                }
                // 分销商检测升级
                event('FenxiaoUpgrade', $data['parent']);
            }
            $res = model('fenxiao')->add($data);
            //修改会员信息
            model('member')->update(['fenxiao_id' => $res, 'is_fenxiao' => 1], [['member_id', '=', $data['member_id']]]);
            model('fenxiao')->commit();
            return $this->success($res);
        } catch (\Exception $e) {
            model('fenxiao')->rollback();
            return $this->error('', $e->getMessage());
        }
    }
    /**
     * 冻结
     * @param $fenxiao_id
     * @return array
     */
    public function frozen($fenxiao_id)
    {
        $data = [
            'status' => -1,
            'lock_time' => time()
        ];
        $res = model('fenxiao')->update($data, [['fenxiao_id', '=', $fenxiao_id]]);
        return $this->success($res);
    }
    /**
     * 解冻
     * @param $fenxiao_id
     * @return array
     */
    public function unfrozen($fenxiao_id)
    {
        $data = [
            'status' => 1,
            'lock_time' => time()
        ];
        $res = model('fenxiao')->update($data, [['fenxiao_id', '=', $fenxiao_id]]);
        return $this->success($res);
    }
    /**
     * 获取分销商详细信息
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getFenxiaoInfo($condition = [], $field = '*')
    {
        $condition[] = ['is_delete','=',0];
        $res = model('fenxiao')->getInfo($condition, $field);
        return $this->success($res);
    }
    /**
     * 获取分销商详细信息
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getFenxiaoDetailInfo($condition = [])
    {
        $condition[] = ['f.is_delete','=',0];
        $field = 'f.*,nm.username,nm.nickname,nm.headimg,nm.source_member';
        $alias = 'f';
        $join = [
            [
                'fenxiao pf',
                'pf.fenxiao_id = f.parent',
                'left'
            ],
            [
                'member nm',
                'nm.member_id = f.member_id',
                'left'
            ]
        ];
        $res = model('fenxiao')->getInfo($condition, $field, $alias, $join);
        $res['parent_name'] = model('member')->getModel()->where('member_id', $res['source_member'])->value('nickname');
        return $this->success($res);
    }
    /**
     * 获取分销列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getFenxiaoList($condition = [], $field = '*', $order = '', $limit = null)
    {
        $condition[] = ['is_delete','=',0];
        $list = model('fenxiao')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }
    /**
     * 获取分销分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getFenxiaoPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '')
    {
        $condition[] = ['f.is_delete','=',0];
        $condition[] = ['', 'exp', \think\facade\Db::raw('m.member_id is not null')];
        $field = 'f.*,m.username,m.source_member';
        $alias = 'f';
        $join = [
            [
                'fenxiao pf',
                'pf.fenxiao_id = f.parent',
                'left'
            ],
            [
                'member m',
                'm.member_id = f.member_id',
                'left'
            ]
        ];
        $list = model('fenxiao')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
        if ($list[ 'list' ]) {
            foreach($list[ 'list' ] as &$row) {
                $row['parent_name'] = '';
                if ($row['source_member'] != 0) {
                    $row['parent_name'] = model('member')->getModel()->where('member_id', $row['source_member'])->value('nickname');
                }
            }
        }

        return $this->success($list);
    }
    /**
     * 获取分销商团队
     * @param int $level
     * @param int $member_id
     * @param number $page
     * @param string $page_size
     */
    public function getFenxiaoTeam($level, $member_id, $page = 1, $page_size = PAGE_LIST_ROWS)
    {
        $condition = '';
        $prefix = config("database")["connections"]["mysql"]["prefix"];
        switch ($level) {
            case 0:
                $condition = "m.source_member = {$member_id} AND m.is_delete = 0";
                break;
            case 1:
                $condition = "m.source_member = {$member_id} AND (f.fenxiao_id is NOT NULL) AND f.is_delete = 0";
                break;
            case 2:
                $two_data = model('member')->query("SELECT GROUP_CONCAT(member_id) as member_id FROM {$prefix}member WHERE is_delete = 0 and is_fenxiao = 1 AND fenxiao_id != 0 AND source_member = {$member_id}");
                if (isset($two_data[0]) && two_data($two_data[0]['member_id']) && !empty($two_data[0]['member_id'])) {
                    $fenxiao_id = $two_data[0]['member_id'];
                    $condition = "m.source_member in ({$fenxiao_id})";
                    $condition .= " AND (f.fenxiao_id is NOT NULL) AND f.is_delete = 0";
                }
                break;
        }
        if (empty($condition)) return $this->success([
            'page_count' => 1,
            'count' => 0,
            'list' => []
        ]);
        $condition .= '';
        $field = 'm.member_id,m.nickname,m.headimg,m.is_fenxiao,m.reg_time,m.order_money,m.order_complete_money,m.order_num,m.order_complete_num,f.fenxiao_id,f.fenxiao_no,f.fenxiao_name,f.audit_time,f.level_name';
        $alias = 'm';
        $join = [
            ['fenxiao f', 'm.member_id = f.member_id', 'left']
        ];
        $list = model('member')->pageList($condition, $field, '', $page, $page_size, $alias, $join);
        return $this->success($list);
    }
    /**
     * 查询我的团队的数量
     * @param unknown $member_id
     * @return array
     */
    public function getFenxiaoTeamNum($member_id, $site_id)
    {
        // 查询分销基础配置
        $config_model = new Config();
        $fenxiao_basic_config = $config_model->getFenxiaoBasicsConfig($site_id);
        $level = $fenxiao_basic_config['data']['value']['level'];
        $prefix = config("database")["connections"]["mysql"]["prefix"];
        $num = 0;
        $member_model = new Member();

        switch ($level) {
            case 1:
                $num = $member_model->getMemberCount([['source_member', '=', $member_id], ['is_fenxiao', '=', 1], ['fenxiao_id', '<>', 0]])['data'];
                break;
            case 2:
                $two_data = $member_model->query("SELECT GROUP_CONCAT(member_id) as member_id FROM {$prefix}member WHERE is_delete = 0 and is_fenxiao = 1 AND fenxiao_id != 0 AND source_member = {$member_id}");
                if (isset($two_data[0]) && isset($two_data[0]['member_id']) && !empty($two_data[0]['member_id'])) {
                    $two_fenxiao_ids = $two_data[0]['member_id'];
                    $num += count(explode(',', $two_fenxiao_ids));
                    $three_data = $member_model->query("SELECT GROUP_CONCAT(member_id) as member_id FROM {$prefix}member WHERE is_delete = 0 and is_fenxiao = 1 AND fenxiao_id != 0 AND source_member IN ({$two_fenxiao_ids})");
                    if (isset($three_data[0]) && isset($three_data[0]['member_id']) && !empty($three_data[0]['member_id'])) {
                        $three_fenxiao_ids = $three_data[0]['fenxiao_id'];
                        $num += count(explode(',', $three_fenxiao_ids));
                    }
                }
                break;
            case 3:
                $two_data = $member_model->query("SELECT GROUP_CONCAT(member_id) as member_id FROM {$prefix}member WHERE is_delete = 0 and is_fenxiao = 1 AND fenxiao_id != 0 AND source_member = {$member_id}");
                if (isset($two_data[0]) && isset($two_data[0]['member_id']) && !empty($two_data[0]['member_id'])) {
                    $two_fenxiao_ids = $two_data[0]['member_id'];
                    $num += count(explode(',', $two_fenxiao_ids));
                    $three_data = $member_model->query("SELECT GROUP_CONCAT(member_id) as member_id FROM {$prefix}member WHERE is_delete = 0 and is_fenxiao = 1 AND fenxiao_id != 0 AND source_member IN ({$two_fenxiao_ids})");
                    if (isset($three_data[0]) && isset($three_data[0]['member_id']) && !empty($three_data[0]['member_id'])) {
                        $three_fenxiao_ids = $three_data[0]['member_id'];
                        $num += count(explode(',', $three_fenxiao_ids));
                        $four_data = model('fenxiao')->query("SELECT GROUP_CONCAT(member_id) as member_id FROM {$prefix}member WHERE is_delete = 0 and is_fenxiao = 1 AND fenxiao_id != 0 AND source_member IN ({$three_fenxiao_ids})");
                        if (isset($four_data[0]) && isset($four_data[0]['member_id']) && !empty($four_data[0]['member_id'])) {
                            $five_fenxiao_ids = $three_data[0]['member_id'];
                            $num += count(explode(',', $five_fenxiao_ids));
                        }
                    }
                }
                break;
        }
        return $this->success($num);
    }
    /**
     * 会员注册之后
     * @param unknown $member_id
     */
    public function memberRegister($member_id, $site_id)
    {
        $config = new Config();
        // 分销商基础设置
        $basics_config = $config->getFenxiaoBasicsConfig($site_id);
        $basics_config = $basics_config['data']['value'];
        // 如果开启分销功能
        if ($basics_config['level']) {
            // 成为分销商的资格
            $fenxiao_config = $config->getFenxiaoConfig($site_id);
            $fenxiao_config = $fenxiao_config['data']['value'];
            // 成为下线条件为：首次点击分享链接成为下线
//     		$config_info = $config->getFenxiaoRelationConfig();
//     		$child_condition = $config_info['data']['value']['child_condition'];
//     		if ($child_condition == 1) {
            $this->bindRelation($member_id);
//     		}
            // 成为分销商无任何条件且无需完善资料 则直接成为分销商
            if (!$fenxiao_config['fenxiao_condition']) {
                // 分销商是否需要审核
                if ($basics_config['is_examine']) {
                    $fenxiao_apply = new FenxiaoApply();
                    $fenxiao_apply->applyFenxiao($member_id, $site_id);
                } else {
                    $this->directlyBecomeFenxiao($member_id);
                }
            }
        }
    }
    /**
     * 自动成为分销商
     * @param unknown $member_id
     */
    public function autoBecomeFenxiao($member_id, $site_id)
    {
        $config = new Config();
        // 分销商基础设置
        $basics_config = $config->getFenxiaoBasicsConfig($site_id);
        $basics_config = $basics_config['data']['value'];
        // 如果开启分销功能
        if ($basics_config['level']) {
            // 成为分销商的资格
            $fenxiao_config = $config->getFenxiaoConfig($site_id);
            $fenxiao_config = $fenxiao_config['data']['value'];
            // 成为分销商无任何条件且无需完善资料 则直接成为分销商
            if (!$fenxiao_config['fenxiao_condition']) {
                // 分销商是否需要审核
                if ($basics_config['is_examine']) {
                    $fenxiao_apply = new FenxiaoApply();
                    $res = $fenxiao_apply->applyFenxiao($member_id, $site_id);
                    return $res;
                } else {
                    $res = $this->directlyBecomeFenxiao($member_id);
                    return $res;
                }
            }
        }
    }
    /**
     * 会员直接成为分销商
     */
    private function directlyBecomeFenxiao($member_id)
    {
        //获取用户信息
        $member_field = 'member_id,site_id,source_member,fenxiao_id,nickname,headimg,mobile,reg_time,order_money,order_complete_money,order_num,order_complete_num';
        $member_info = model('member')->getInfo([['member_id', '=', $member_id]], $member_field);
        if (!empty($member_info)) {
            $parent = 0;
            if (!empty($member_info['source_member'])) {
                $fenxiao_info = model('fenxiao')->getInfo([['member_id', '=', $member_info['source_member']],['is_delete','=',0]], 'fenxiao_id');
                if (!empty($fenxiao_info)) $parent = $fenxiao_info['fenxiao_id'];
            }
            //获取分销等级信息
            $level_model = new FenxiaoLevel();
            $level_info = $level_model->getLevelInfo([['site_id', '=', $member_info['site_id'] ], ['is_default', '=', 1]], 'level_id,level_name');
            $data = [
                'site_id' => $member_info['site_id'],
                'fenxiao_name' => $member_info['nickname'],
                'mobile' => $member_info['mobile'],
                'member_id' => $member_info['member_id'],
                'parent' => $parent,
                'level_id' => $level_info['data']['level_id'],
                'level_name' => $level_info['data']['level_name']
            ];
            $res = $this->addFenxiao($data);
            return $res;
        }
    }
    /**
     * 绑定上下线关系
     * @param unknown $member_id
     */
    public function bindRelation($member_id)
    {
        $member_info = model('member')->getInfo([['member_id', '=', $member_id], ['fenxiao_id', '=', '']], 'member_id,source_member');
        if (!empty($member_info) && !empty($member_info['source_member'])) {
            // 查询推荐人是否是分销商
            $fenxiao_info = model('fenxiao')->getInfo([['member_id', '=', $member_info['source_member']],['is_delete','=',0]], 'fenxiao_id,parent');
            if (!empty($fenxiao_info)) {
                model('member')->startTrans();
                try {
                    $member_data = [
                        'fenxiao_id' => $fenxiao_info['fenxiao_id']
                    ];
                    model('member')->update($member_data, [['member_id', '=', $member_id]]);
                    model('fenxiao')->setInc([['fenxiao_id', '=', $fenxiao_info['fenxiao_id']]], 'one_child_num');
                    // 分销商检测升级
                    event('FenxiaoUpgrade', $fenxiao_info['fenxiao_id']);
                    model('member')->commit();
                } catch (\Exception $e) {
                    model('member')->rollback();
                }
            }
        }
    }
    /**
     * 分销商检测升级
     * @param unknown $fenxiao_id
     */
    public function fenxiaoUpgrade($fenxiao_id)
    {
        $join = [
            ['member m', 'f.member_id = m.member_id', 'inner'],
            ['fenxiao_level fl', 'f.level_id = fl.level_id', 'inner']
        ];
        $fenxiao_info = model('fenxiao')->getInfo([['f.fenxiao_id', '=', $fenxiao_id], ['f.status', '=', 1],['f.is_delete','=',0]], 'f.level_id,m.order_num,m.order_money,f.one_fenxiao_order_num,f.one_fenxiao_order_money,f.one_child_num,f.one_child_fenxiao_num,fl.one_rate,fl.level_num', 'f', $join);
        if (!empty($fenxiao_info)) {
            $level_list = model('fenxiao_level')->getList([ ['level_num', '>', $fenxiao_info['level_num']] ], '*', 'level_num asc,one_rate asc');
            if (!empty($level_list)) {
                $upgrade_level = null;
                foreach ($level_list as $item) {
                    if ($item['upgrade_type'] == 2) {
                        if ($fenxiao_info['order_num'] >= $item['order_num'] && $fenxiao_info['order_money'] >= $item['order_money'] && $fenxiao_info['one_fenxiao_order_num'] >= $item['one_fenxiao_order_num'] && $fenxiao_info['one_fenxiao_order_money'] >= $item['one_fenxiao_order_money'] && $fenxiao_info['one_child_num'] >= $item['one_child_num'] && $fenxiao_info['one_child_fenxiao_num'] >= $item['one_child_fenxiao_num']) {
                            $upgrade_level = $item;
                            break;
                        }
                    } else {
                        if (($fenxiao_info['order_num'] >= $item['order_num'] && $item['order_num'] > 0) || ($fenxiao_info['order_money'] >= $item['order_money'] && $item['order_money'] > 0) || ($fenxiao_info['one_fenxiao_order_num'] >= $item['one_fenxiao_order_num'] && $item['one_fenxiao_order_num'] > 0) || ($fenxiao_info['one_fenxiao_order_money'] >= $item['one_fenxiao_order_money'] && $item['one_fenxiao_order_money'] > 0) || ($fenxiao_info['one_child_num'] >= $item['one_child_num'] && $item['one_child_num'] > 0) || ($fenxiao_info['one_child_fenxiao_num'] >= $item['one_child_fenxiao_num'] && $item['one_child_fenxiao_num'] > 0)) {
                            $upgrade_level = $item;
                            break;
                        }
                    }
                }
                if ($upgrade_level) {
                    model('fenxiao')->update(['level_id' => $upgrade_level['level_id'], 'level_name' => $upgrade_level['level_name']], [['fenxiao_id', '=', $fenxiao_id]]);
                }
            }
        }
    }
    /**
     * 获取下一个可升级的分销商等级 及当前分销商已达成的条件
     * @param $member_id
     * @param $site_id
     */
    public function geFenxiaoNextLevel($member_id, $site_id)
    {
        $array = [];
        $join = [
            ['member m', 'f.member_id = m.member_id', 'inner'],
            ['fenxiao_level fl', 'f.level_id = fl.level_id', 'inner']
        ];
        $fenxiao_info = model('fenxiao')->getInfo(
            [['f.member_id', '=', $member_id], ['f.site_id', '=', $site_id], ['f.status', '=', 1],['f.is_delete','=',0]],
            'f.level_id,m.order_num,m.order_money,f.one_fenxiao_order_num,f.one_fenxiao_order_money,f.one_child_num,f.one_child_fenxiao_num,fl.one_rate,fl.level_num', 'f', $join
        );
        $array['fenxiao'] = $fenxiao_info;
        $last_level = [];
        if (!empty($fenxiao_info)) {
            $last_level = model('fenxiao_level')->getFirstData([['level_num', '>=', $fenxiao_info['level_num']], ['level_id', '<>', $fenxiao_info['level_id']]], '*', 'level_num asc,one_rate asc');
        }
        $array['last_level'] = $last_level;
        return $this->success($array);
    }
    /**
     * 变更上下级关系
     * @param $member_id
     * @param $parent
     */
    public function changeParentFenxiao($member_id, $parent)
    {
        if ($member_id == '' || $member_id == 0) {
            return $this->error('', '参数member_id不能为空');
        }
        if ($parent == '' || $parent == 0) {
            return $this->error('', '上级分销商不能为空');
        }
        //获取上级分销商id
        $parent_info = model('fenxiao')->getInfo([['fenxiao_id', '=', $parent],['is_delete','=',0]]);
        if (empty($parent_info)) {
            return $this->error('', '上级分销商不存在');
        }
        //用户信息
        $member_info = model('member')->getInfo([['member_id', '=', $member_id]], 'fenxiao_id,is_fenxiao');
        if (empty($member_info)) {
            return $this->error('', '用户不存在');
        }
        model('fenxiao')->startTrans();
        try {
            if ($member_info['is_fenxiao'] == 1) {//是分销商
                $fenxiao_info = model('fenxiao')->getInfo(
                    [['fenxiao_id', '=', $member_info['fenxiao_id']],['is_delete','=',0]],
                    'parent'
                );
                //修改原有上级分销商团队人数
                if ($fenxiao_info['parent'] > 0) {
                    //获取原有上级分销商信息
                    model('fenxiao')->setDec([['fenxiao_id', '=', $fenxiao_info['parent']]], 'one_child_fenxiao_num');
                }
                //修改变更后的上级分销商团队人数
                model('fenxiao')->setInc([['fenxiao_id', '=', $parent]], 'one_child_fenxiao_num');
                //修改上级分销商
                model('fenxiao')->update(['parent' => $parent], [['fenxiao_id', '=', $member_info['fenxiao_id']]]);
            } else {//不是分销商
                //修改上级分销商
                model('member')->update(['fenxiao_id' => $parent, 'source_member' => $parent_info['member_id']], [['member_id', '=', $member_id]]);
                //修改变更后的上级分销商团队人数
                model('fenxiao')->update(['one_child_num' => $parent_info['one_child_num'] + 1], [['fenxiao_id', '=', $parent]]);
            }
            model('fenxiao')->commit();
            return $this->success();
        } catch (\Exception $e) {
            model('fenxiao')->rollback();
            return $this->error('', $e->getMessage());
        }
    }
    /**
     * 获取上级分销商名称
     * @param $fenxiao_id
     * @param int $type 1-上级
     * @return mixed|string
     */
    public function getParentFenxiaoName($fenxiao_id, $type = 1)
    {
        if($fenxiao_id == 0){
            return '';
        }
        if ($type == 1) {
            $fenxiao_name = model('fenxiao')->getValue([['fenxiao_id', '=', $fenxiao_id]], 'fenxiao_name');
            return $fenxiao_name;
        }else{
            $parent = model('fenxiao')->getValue([['fenxiao_id', '=', $fenxiao_id]],'parent');
            if($parent == 0){
                return '';
            }else{
                $fenxiao_name = model('fenxiao')->getValue([['fenxiao_id', '=', $parent]], 'fenxiao_name');
                return $fenxiao_name;
            }
        }
    }
    /**
     * 会员注销删除分销商
     * @param $member_id
     * @param $site_id
     * @return array
     */
    public function CronMemberCancel($member_id,$site_id)
    {
        $info = model('fenxiao')->getInfo([['member_id','=',$member_id],['site_id','=',$site_id]]);
        if(empty($info)){
            return $this->success();
        }
        //冻结账户并删除
        $data = [
            'status' => -1,
            'lock_time' => time(),
            'is_delete' => 1
        ];
        $res = model('fenxiao')->update($data,[['fenxiao_id','=',$info['fenxiao_id']]]);
        return $this->success($res);
    }
}