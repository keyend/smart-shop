<?php
namespace addon\notes\shop\controller;
use app\shop\controller\BaseShop;
use addon\notes\model\Notes as NotesModel;
use addon\notes\model\Group as GroupModel;
/**
 * 笔记控制器
 */
class Notes extends BaseShop
{
    /*
     *  笔记活动列表
     */
    public function lists()
    {
        $model = new NotesModel();
        //获取续签信息
        if (request()->isAjax()) {
            $condition[] = ['pn.site_id', '=', $this->site_id];
            //笔记状态
            $status = input('status', '');
            if ($status !== '') {
                $condition[] = ['pn.status', '=', $status];
            }
            //笔记标题
            $note_title = input('note_title', '');
            if ($note_title) {
                $condition[] = ['pn.note_title', 'like', '%' . $note_title . '%'];
            }
            //笔记类型
            $note_type = input('note_type', '');
            if ($note_type) {
                $condition[] = ['pn.note_type', '=', $note_type];
            }
            //分组
            $group_id = input('group_id', '');
            if ($group_id) {
                $condition[] = ['pn.group_id', '=', $group_id];
            }
            $page      = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $list      = $model->getNotesPageList($condition, $page, $page_size, 'pn.sort asc');
            return $list;
        } else {
            //笔记类型
            $note_type = $model->getNoteType();
            $this->assign('note_type', $note_type);
            //笔记分组
            $group_model = new GroupModel();
            $group_list  = $group_model->getNotesGroupList([['site_id', '=', $this->site_id]], 'group_id,group_name');
            $this->assign('group_list', $group_list['data']);
            return $this->fetch("notes/lists");
        }
    }
    /**
     * 添加活动
     */
    public function add()
    {
        $note_type = input('note_type', '');
        if (request()->isAjax()) {
            $notes_data  = [
                'site_id'              => $this->site_id,
                'note_type'            => $note_type,
                'note_title'           => input('note_title', ''),
                'note_abstract'        => input('note_abstract', ''),
                'group_id'             => input('group_id', ''),
                'cover_type'           => input('cover_type', ''),
                'cover_img'            => input('cover_img', ''),
                'goods_ids'            => input('goods_ids', ''),
                'goods_highlights'     => input('goods_highlights', ''),
                'note_content'         => input('note_content', ''),
                'status'               => input('status', ''),
                'is_show_release_time' => input('is_show_release_time', ''),
                'is_show_read_num'     => input('is_show_read_num', ''),
                'is_show_dianzan_num'  => input('is_show_dianzan_num', ''),
                'initial_read_num'     => input('initial_read_num', ''),
                'initial_dianzan_num'  => input('initial_dianzan_num', '')
            ];
            $notes_model = new NotesModel();
            return $notes_model->addNotes($notes_data);
        } else {
            $this->assign('note_type', $note_type);
            //笔记分组
            $group_model = new GroupModel();
            $group_list  = $group_model->getNotesGroupList([['site_id', '=', $this->site_id]], 'group_id,group_name');
            $this->assign('group_list', $group_list['data']);
            if ($note_type == 'goods_item') {
                return $this->fetch('notes/add_goods_item');
            } else {
                return $this->fetch('notes/add_shop_said');
            }
        }
    }
    /*
     * 编辑活动
     */
    public function edit()
    {
        $notes_model = new NotesModel();
        $note_id     = input('note_id', '');
        $note_type   = input('note_type', '');
        if (request()->isAjax()) {
            $notes_data = [
                'note_id'              => $note_id,
                'site_id'              => $this->site_id,
                'note_type'            => $note_type,
                'note_title'           => input('note_title', ''),
                'note_abstract'        => input('note_abstract', ''),
                'group_id'             => input('group_id', ''),
                'cover_type'           => input('cover_type', ''),
                'cover_img'            => input('cover_img', ''),
                'goods_ids'            => input('goods_ids', ''),
                'goods_highlights'     => input('goods_highlights', ''),
                'note_content'         => input('note_content', ''),
                'status'               => input('status', ''),
                'is_show_release_time' => input('is_show_release_time', ''),
                'is_show_read_num'     => input('is_show_read_num', ''),
                'is_show_dianzan_num'  => input('is_show_dianzan_num', ''),
                'initial_read_num'     => input('initial_read_num', ''),
                'initial_dianzan_num'  => input('initial_dianzan_num', '')
            ];
            return $notes_model->editNotes($notes_data);
        } else {
            $this->assign('note_id', $note_id);
            $this->assign('note_type', $note_type);
            //笔记分组
            $group_model = new GroupModel();
            $group_list  = $group_model->getNotesGroupList([['site_id', '=', $this->site_id]], 'group_id,group_name');
            $this->assign('group_list', $group_list['data']);
            //获取笔记信息
            $note_info = $notes_model->getNotesDetailInfo([['note_id', '=', $note_id], ['site_id', '=', $this->site_id]]);
            $this->assign('info', $note_info['data']);
            if ($note_type == 'goods_item') {
                return $this->fetch('notes/edit_goods_item');
            } else {
                return $this->fetch('notes/edit_shop_said');
            }
        }
    }
    /*
     *  删除笔记活动
     */
    public function delete()
    {
        $note_id = input('note_id', '');
        $notes_model = new NotesModel();
        return $notes_model->deleteNotes([['note_id', '=', $note_id], ['site_id', '=', $this->site_id]]);
    }
    /**
     * 草稿箱
     * @return mixed
     */
    public function drafts()
    {
        $model = new NotesModel();
        //笔记类型
        $note_type = $model->getNoteType();
        $this->assign('note_type', $note_type);
        //笔记分组
        $group_model = new GroupModel();
        $group_list  = $group_model->getNotesGroupList([['site_id', '=', $this->site_id]], 'group_id,group_name');
        $this->assign('group_list', $group_list['data']);
        return $this->fetch("notes/drafts");
    }
    /**
     * 修改商品类型排序
     */
    public function modifySort()
    {
        if (request()->isAjax()) {
            $sort = input('sort', 0);
            $note_id = input('note_id', 0);
            $notes_model = new NotesModel();
            return $notes_model->modifyNotesSort($sort, $note_id, $this->site_id);
        }
    }
    /**
     * 笔记选择组件
     * @return \multitype
     */
    public function notesSelect()
    {
        $model = new NotesModel();
        if (request()->isAjax()) {
            $condition[] = ['pn.site_id', '=', $this->site_id];
            //笔记状态
            $status = input('status', '');
            if ($status !== '') {
                $condition[] = ['pn.status', '=', $status];
            }
            //笔记标题
            $note_title = input('note_title', '');
            if ($note_title) {
                $condition[] = ['pn.note_title', 'like', '%' . $note_title . '%'];
            }
            //笔记类型
            $note_type = input('note_type', '');
            if ($note_type) {
                $condition[] = ['pn.note_type', '=', $note_type];
            }
            //分组
            $group_id = input('group_id', '');
            if ($group_id) {
                $condition[] = ['pn.group_id', '=', $group_id];
            }
            $page      = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $list      = $model->getNotesPageList($condition, $page, $page_size, 'pn.sort asc');
            return $list;
        } else {
            //已经选择的商品sku数据
            $select_id = input('select_id', '');
            $max_num = input('max_num', 0);
            $min_num = input('min_num', 0);
            $this->assign('select_id', $select_id);
            $this->assign('max_num', $max_num);
            $this->assign('min_num', $min_num);
            //笔记类型
            $note_type = $model->getNoteType();
            $this->assign('note_type', $note_type);
            //笔记分组
            $group_model = new GroupModel();
            $group_list  = $group_model->getNotesGroupList([['site_id', '=', $this->site_id]], 'group_id,group_name');
            $this->assign('group_list', $group_list['data']);
            return $this->fetch("notes/notes_select");
        }
    }
}