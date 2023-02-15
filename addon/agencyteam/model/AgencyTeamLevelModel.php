<?php
namespace addon\agencyteam\model;
use addon\agencyteam\Model;
use think\facade\Cache;
use app\model\system\Config as ConfigModel;

class AgencyTeamLevelModel extends Model
{
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'agency_level';

    /**
     * 主键名称
     * @var string
     */
    protected $pk = 'id';

    protected function __init()
    {
        $this->_model = model('agency_level');
    }

    /**
     * 获取团队等级分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        return $this->success($this->_model->pageList($condition, $field, $order, $page, $page_size));
    }

    /**
     * 获取团队商等级列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getLevelList($condition = [], $field = '*', $order = '', $limit = null)
    {
        return $this->success($this->_model->getList($condition, $field, $order, '', '', '', $limit));
    }

    /**
     * 获取团队等级信息
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getLevelInfo($condition = [], $field = '*')
    {
        $level = $this->_model->getInfo($condition, $field);
        $level['bonus_condition'] = json_decode($level['bonus_condition'], true);
        $level['subsidy_condition'] = json_decode($level['subsidy_condition'], true);
        $level['condition'] = json_decode($level['condition'], true);

        return $this->success($level);
    }

    /**
     * 添加等级
     * @param $data
     * @return array
     */
    public function addLevel($data)
    {
        $data['create_time'] = time();
        $data['status']      = 1;
        $res = $this->_model->add($data);
        $this->getLevel(true);
        return $this->success($res);
    }

    /**
     * 编辑团队等级
     * @param $data
     * @param array $condition
     * @return array
     */
    public function editLevel($data, $condition = [])
    {
        $res = $this->where($condition)->update($data);
        if ($res) {
            if (isset($data['levelname']) && $data['levelname'] != '') {
                model('agency')->update(['levelname' => $data['levelname']], $condition);
                $this->getLevel(true);
            }
        }
        return $this->success($res);
    }

    /**
     * 删除团队等级
     * @param array $condition
     * @return array
     */
    public function deleteLevel($level_id, $site_id)
    {
        $usage_list  = AgencyModel::where([['level', '=', $level_id]])->field('id')->find();
        if (empty($usage_list)) {
            $res = $this->_model->delete([['id', '=', $level_id], ['site_id', '=', $site_id]]);
            $this->getLevel(true);
            return $this->success($res);
        } else {
            return $this->error('', '该团队等级存在其他代理团队，无法删除');
        }
    }

    /**
     * 刷新缓存
     *
     * @param boolean $rewrite
     * @return void
     */
    public function getLevel($rewrite = false)
    {
        error_reporting(E_ERROR);
        $cache = Cache::get("addon_agency_level");
        if (empty($cache) || $rewrite != false) {
            $condition = [];
            $condition[] = ['status', '=', 1];
            $level_list = $this->_model->pageList($condition, '*', 'level ASC', 1, 9999);
            $cache = $level_list['list'];
            foreach($cache as $i => $row) {
                $row['bonus_condition'] = json_decode($row['bonus_condition'], true);
                $row['subsidy_condition'] = json_decode($row['subsidy_condition'], true);
                $row['condition'] = json_decode($row['condition'], true);
                $row['subsidy_condition']['achievement'] = (float)$row['subsidy_condition']['achievement'];
                $row['subsidy_condition']['children'] = (int)$row['subsidy_condition']['children'];
                $row['subsidy_condition']['samepeer'] = (float)$row['subsidy_condition']['samepeer'];
                $row['subsidy_condition']['usertotal'] = (int)$row['subsidy_condition']['usertotal'];
                $row['condition']['action'] = (int)$row['condition']['action'];
                $row['condition']['days'] = (int)$row['condition']['days'];
                $row['condition']['people'] = (int)$row['condition']['people'];
                $cache[$i] = $row;
            }
            Cache::tag("addon_agency")->set("addon_agency_level", $cache);
        }

        return $cache;
    }

    /**
     * 返回等级策略
     *
     * @param array $ids
     * @return void
     */
    public function getInfo($ids)
    {
        $res = [];
        $lst = self::where('level', 'IN', $ids)->where('status', 1)->field('level,rate_subsidy,subsidy_condition')->select()->toArray();

        foreach($lst as $row) {
            $row['subsidy_condition'] = json_decode($row['subsidy_condition'], true);
            $row['subsidy_condition']['achievement'] = (float)$row['subsidy_condition']['achievement'];
            $row['subsidy_condition']['children'] = (int)$row['subsidy_condition']['children'];
            $row['subsidy_condition']['samepeer'] = (float)$row['subsidy_condition']['samepeer'];
            $row['subsidy_condition']['usertotal'] = (int)$row['subsidy_condition']['usertotal'];
            $res[$row['level']] = $row;
        }

        return $res;
    }
}