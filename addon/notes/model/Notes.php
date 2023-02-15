<?php
namespace addon\notes\model;
use app\model\BaseModel;
/**
 * 笔记
 */
class Notes extends BaseModel
{
    //笔记类型
    private $note_type = [
        ['type' => 'shop_said', 'name' => '掌柜说'],
        ['type' => 'goods_item', 'name' => '单品介绍']
    ];
    /**
     * 获取笔记类型
     * @return array
     */
    public function getNoteType()
    {
        return $this->note_type;
    }
    /**
     * 添加笔记
     * @param $data
     * @return array
     */
    public function addNotes($data)
    {
        $data['create_time'] = time();
        model('notes')->startTrans();
        try {
            //添加笔记
            model('notes')->add($data);
            //更新分组笔记数等信息
            if ($data['status'] == 1) {
                model('notes_group')->setInc([['group_id', '=', $data['group_id']]], 'notes_num');
                model('notes_group')->setInc([['group_id', '=', $data['group_id']]], 'release_num');
            } else {
                model('notes_group')->setInc([['group_id', '=', $data['group_id']]], 'notes_num');
            }
            model('notes')->commit();
            return $this->success();
        } catch (\Exception $e) {
            model('notes')->rollback();
            return $this->error('', $e->getMessage());
        }
    }
    /**
     * 编辑笔记
     * @param $condition
     * @param $data
     * @return array
     */
    public function editNotes($data)
    {
        $data['update_time'] = time();
        model('notes')->startTrans();
        try {
            //添加笔记
            model('notes')->update($data, [['site_id', '=', $data['site_id']], ['note_id', '=', $data['note_id']]]);
            //更新分组笔记数等信息
            if ($data['status'] == 1) {
                model('notes_group')->setInc([['group_id', '=', $data['group_id']]], 'release_num');
            }
            model('notes')->commit();
            return $this->success();
        } catch (\Exception $e) {
            model('notes')->rollback();
            return $this->error('', $e->getMessage());
        }
    }
    /**
     * 删除笔记
     * @param $note_id
     * @param $site_id
     * @return array|\multitype
     */
    public function deleteNotes($condition)
    {
        //笔记数
        $notes_info = model('notes')->getInfo($condition, 'group_id,status');
        if (empty($notes_info)) {
            return $this->success('', '数据不合法');
        } else {
            model('notes')->startTrans();
            try {
                //删除笔记
                model('notes')->delete($condition);
                //更新分组笔记数等信息
                if ($notes_info['status'] == 1) {
                    model('notes_group')->setDec([['group_id', '=', $notes_info['group_id']]], 'notes_num');
                    model('notes_group')->setDec([['group_id', '=', $notes_info['group_id']]], 'release_num');
                } else {
                    model('notes_group')->setDec([['group_id', '=', $notes_info['group_id']]], 'notes_num');
                }
                model('notes')->commit();
                return $this->success();
            } catch (\Exception $e) {
                model('notes')->rollback();
                return $this->error('', $e->getMessage());
            }
        }
    }
    /**
     * 修改排序
     * @param int $sort
     * @param int $class_id
     */
    public function modifyNotesSort($sort, $note_id, $site_id)
    {
        $res = model('notes')->update([ 'sort' => $sort ], [ [ 'note_id', '=', $note_id ], [ 'site_id', '=', $site_id ] ]);
        return $this->success($res);
    }
    /**
     * 获取笔记信息
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getNotesInfo($condition = [], $field = '*')
    {
        $info = model("notes")->getInfo($condition, $field);
        return $this->success($info);
    }
    /**
     * 获取笔记信息
     * @param array $condition
     * @param string $field
     * @param int $type
     * @return array
     */
    public function getNotesDetailInfo($condition = [], $field = '*', $type = 1)
    {
        $info = model('notes')->getInfo($condition, $field);
        if (!empty($info)) {
            $goods_field        = 'sku_id,goods_name,goods_stock,price,goods_image';
            $goods_list         = model('goods')->getList([['site_id', '=', $info['site_id']], ['goods_id', 'in', $info['goods_ids']]], $goods_field);
            $info['goods_list'] = $goods_list;
        }
        //添加浏览记录
        if ($type == 2) {
            model('notes')->setInc($condition, 'read_num', 1);
        }
        return $this->success($info);
    }
    /**
     * 获取笔记列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getNotesList($condition = [], $field = '*', $order = '', $limit = null)
    {
        $list = model('notes')->getList($condition, $field, $order, '', '', '', $limit);
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
    public function getNotesPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'pn.sort asc')
    {
        $field = 'pn.*,png.group_name';
        $alias = 'pn';
        $join  = [
            [
                'notes_group png',
                'png.group_id = pn.group_id',
                'left'
            ]
        ];
        $list  = model('notes')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
        return $this->success($list);
    }
}