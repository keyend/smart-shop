<?php
namespace addon\agencyteam\model;
use addon\agencyteam\Model;
use app\model\system\Config as ConfigModel;

class AgencyTeamMapsModel extends Model
{
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'agency_maps';

    /**
     * 主键名称
     * @var string
     */
    protected $pk = 'id';

   /**
    * 无限向下遍历树形
    *
    * @param collect $model
    * @param string  $pk
    * @access private
    * @return array [1,2,3,4,5,6]
    */
    private function forMapIds($value = '0', $condition = [], $pk = 'member_id', $pid = 'parent_id') 
    {
        static $model = null;

        if ($model == null) {
            try {
                $this->query('SET GLOBAL group_concat_max_len=102400;');
                $this->query('SET SESSION group_concat_max_len=102400;');
            } catch(\Exception $e) {
                // PASS
            }

            $model = app()->make(AgencyTeamModel::class);
        }

        $ids = $model->where($pid, '=', $value)->where($condition)->value("GROUP_CONCAT(`{$pk}`)");

        if (!empty($ids)) {
            $cids = $ids;

            if ($cids != '') {
                while(!empty($cids)) {
                    $cids = $model->where($pid, 'IN', $cids)->where($condition)->value("GROUP_CONCAT(`{$pk}`)");
                    if ($cids === false) {
                        throw new \Exception('错误: 超出!');
                    } elseif ($cids != '') {
                        if (substr($cids, -1) == ',') {
                            $cids = substr($cids, 0, strlen($cids) - 1);
                        }

                        $ids .= ",{$cids}";
                    } else {
                        break;
                    }
                }
            }
        }

        return explode(",", $ids);
    }

    /**
     * 获取团队成员列表
     *
     * @param int $id 团队长ID
     * @param string $filter 过滤器
     * @return array
     */
    private function getPerformanceIds($id, $condition = [])
    {
        return array_filter($this->forMapIds($id, $condition, 'member_id', 'parent_id'));
    }

    /**
     * 重新映射缓存
     *
     * @param array $ids
     * @return void
     */
    public function updateAgencyTeamRelationMaps($ids = [])
    {
        if (!empty($ids)) {
            $this->where('member_id', 'IN', $ids)->delete();
            $this->query("INSERT INTO " . config('database.connections.mysql.prefix') . "{$this->name} (`member_id`,`companion`) SELECT member_id,companion FROM " . config('database.connections.mysql.prefix') . "agency WHERE member_id IN (" . implode(',', $ids) . ")");
        } else {
            $fetchData = $this->query("SELECT GROUP_CONCAT(ag.member_id) as `ids` FROM " . config('database.connections.mysql.prefix') . "agency ag LEFT JOIN " . config('database.connections.mysql.prefix') . "{$this->name} agm ON (agm.member_id = ag.member_id) WHERE (agm.id is NULL) OR (ag.companion != agm.companion)");
            if ($fetchData) {
                $ids = $fetchData[0]['ids'];
            }
        }

        $query = new static;
        $model = app()->make(AgencyTeamModel::class);

        foreach($ids as $id) {
            $values = $this->getPerformanceIds($id);
            $length = count($values);
            $query->where('member_id', $id)->update([
                'companion' => $length,
                'maps' => implode(',', $values)
            ]);
            $model->where('member_id', $id)->update([
                'companion' => $length
            ]);
        }
    }

    /**
     * 返回父级列表
     *
     * @param int $id 团队长ID
     * @return void
     */
    public function getParents($member_id)
    {
        $user_account_data = $this->where('member_id', $member_id)->find();
        if (!$user_account_data) {
            $this->rebuild($member_id);
            return $this->getParents($member_id);
        }

        // return $this->where('maps', 'LIKE', "%,{$member_id}")->whereOr('maps', 'LIKE', "%,{$member_id},%")
        //             ->whereOr('maps', 'LIKE', "{$member_id},%")->value('GROUP_CONCAT(`member_id`)');

        $user_model = app()->make(AgencyTeamModel::class);
        $user_data = $user_model->where('member_id', $member_id)->field('level,member_id,parent_id')->find();
        $parents = [];
        $level = 0;
        /**
         * 等级拦截，当前等级大于上层等级时，拦截
         * @abstract
         */
        while(!empty($user_data)) {
            $user_data['level'] = intval($user_data['level']);
            if ($level <= $user_data['level']) {
                $level = max($level, $user_data['level']);
                $parents[] = $user_data['member_id'];
            }

            if ($user_data['parent_id'] != 0) {
                $user_data = $user_model->where('member_id', $user_data['parent_id'])->field('level,member_id,parent_id')->find();
            } else {
                $user_data = null;
            }
        }

        return $parents;
    }

    /**
     * 自上而下重建
     *
     * @param int $id 团队长ID
     * @return void
     */
    public function rebuild($member_id)
    {
        $user_model = app()->make(AgencyTeamModel::class);
        $ids = [];
        $parent_id = (int)$user_model->where('member_id', $member_id)->value('parent_id');
        // 向上到最顶部
        while($parent_id != 0) {
            $ids[] = $parent_id;
            $parent_id = (int)$user_model->where('member_id', $parent_id)->value('parent_id');
        }
        // 加入自已
        $ids[] = $member_id;
        // 向下到最低部
        $children_ids = $user_model->where('parent_id', $member_id)->value('GROUP_CONCAT(`member_id`)');
        while(!empty($children_ids)) {
            $children_ids = explode(',', $children_ids);
            $ids = array_merge($ids, $children_ids);
            $children_ids = $user_model->where('parent_id', 'IN', $children_ids)->value('GROUP_CONCAT(`member_id`)');
        }

        sort($ids);

        $this->updateAgencyTeamRelationMaps($ids);
    }

    /**
     * 清除冗余记录
     *
     * @return void
     */
    public function clearRedundancyMaps()
    {
        $query = new static();
        $ids = $query->alias('gm')->join('agency ag', 'gm.member_id = ag.member_id', 'left')->whereExp("ag.member_id", 'IS NULL')->value('GROUP_CONCAT(gm.id)');
        if (!empty($ids)) {
            $query->query("DELETE FROM " . config('database.connections.mysql.prefix') . "{$this->name} WHERE id IN ({$ids})");
        }
    }
}