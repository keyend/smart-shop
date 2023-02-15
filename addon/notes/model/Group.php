<?php
namespace addon\notes\model;
use app\model\BaseModel;
/**
 * 笔记分组
 */
class Group extends BaseModel
{
    /**
     * 添加笔记分组
     * @param $data
     * @return array
     */
    public function addNotesGroup($data)
    {
        $data['create_time'] = time();
        $res = model('notes_group')->add($data);
        return $this->success($res);
    }
    /**
     * 编辑笔记分组
     * @param $condition
     * @param $data
     * @return array
     */
    public function editNotesGroup($condition, $data)
    {
        $data['update_time'] = time();
        $res = model('notes_group')->update($data, $condition);
        return $this->success($res);
    }
    /**
     * 删除笔记分组
     * @param $notes_id
     * @param $site_id
     * @return array|\multitype
     */
    public function deleteNotesGroup($group_id, $site_id)
    {
        //笔记数
        $notes_count = model('notes')->getCount([['group_id', '=', $group_id], ['site_id', '=', $site_id]]);
        if ($notes_count > 0) {
            return $this->error('', '该分组下存在店铺笔记，咱不能删除');
        } else {
            $res = model('notes_group')->delete([['group_id', '=', $group_id], ['site_id', '=', $site_id]]);
            return $this->success($res);
        }
    }
    /**
     * 获取笔记分组信息
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getNotesGroupInfo($condition = [], $field = '*')
    {
        $info = model("notes_group")->getInfo($condition, $field);
        return $this->success($info);
    }
    /**
     * 获取笔记分组列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getNotesGroupList($condition = [], $field = '*', $order = '', $limit = null)
    {
        $list = model('notes_group')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }
    /**
     * 获取笔记分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getNotesGroupPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $list = model('notes_group')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }
}