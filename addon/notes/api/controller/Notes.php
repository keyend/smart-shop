<?php
namespace addon\notes\api\controller;
use addon\notes\model\Group;
use app\api\controller\BaseApi;
use addon\notes\model\Notes as NotesModel;
use addon\notes\model\Record as RecordModel;
class Notes extends BaseApi
{
    /**
     *  获取笔记分组
     */
    public function group()
    {
        $model = new Group();
        $list = $model->getNotesGroupList([['site_id', '=', $this->site_id]], 'group_id,group_name,notes_num,release_num', 'sort asc');
        return $this->response($list);
    }
    /**
     * 获取文章列表
     */
    public function lists()
    {
        $token = $this->checkToken();
        $page = isset($this->params['page']) ? $this->params['page'] : 1;
        $page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
        $group_id = isset($this->params['group_id']) ? $this->params['group_id'] : '';
		$goods_id_arr = isset($this->params['goods_id_arr']) ? $this->params['goods_id_arr'] : '';
        $condition[] = ['pn.site_id', '=', $this->site_id];
        if ($group_id) {
            $condition[] = ['pn.group_id', '=', $group_id];
        }
		
		if(!empty($goods_id_arr)){
		    $condition[] = ['pn.note_id', 'in', $goods_id_arr];
		}
        $note_model = new NotesModel();
        $list_result = $note_model->getNotesPageList($condition, $page, $page_size);
        if($token['code'] >= 0){
            $list = $list_result['data']['list'];
            $record_model = new RecordModel();
            foreach($list as $k=>$v){
                //获取用户是否点赞
                $is_dianzan = $record_model->getIsDianzan($v['note_id'],$this->member_id);
                $list[$k]['is_dianzan'] = $is_dianzan['data'];
            }
            $list_result['data']['list'] = $list;
        }
        return $this->response($list_result);
    }
    /**
     * 文章详情
     */
    public function detail()
    {
        $token = $this->checkToken();
        $note_id = isset($this->params['note_id']) ? $this->params['note_id'] : '';
        if (empty($note_id)) {
            return $this->response($this->error('', 'REQUEST_NOTE_ID'));
        }
        $condition = [
            ['site_id', '=', $this->site_id],
            ['note_id', '=', $note_id]
        ];
        $note_model = new NotesModel();
        $info_result = $note_model->getNotesDetailInfo($condition, '*', 2);
        if($token['code'] >= 0){
            $info = $info_result['data'];
            $record_model = new RecordModel();
            $is_dianzan = $record_model->getIsDianzan($info['note_id'],$this->member_id);
            $info['is_dianzan'] = $is_dianzan['data'];
            $info_result['data'] = $info;
        }
        return $this->response($info_result);
    }
}