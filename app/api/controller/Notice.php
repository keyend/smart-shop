<?php
namespace app\api\controller;
use app\model\web\Notice as NoticeModel;
/**
 *
 * @author Administrator
 *
 */
class Notice extends BaseApi
{
    /**
     * 基础信息
     */
    public function info()
    {
        $id = isset($this->params['id']) ? $this->params['id'] : 0;
        if (empty($id)) {
            return $this->response($this->error('', 'REQUEST_ID'));
        }
        $notice = new NoticeModel();
        $info   = $notice->getNoticeInfo([['id', '=', $id], ['site_id', '=', $this->site_id]]);
        return $this->response($info);
    }
    public function lists()
    {
        $id_arr = isset($this->params['id_arr']) ? $this->params['id_arr'] : '';//id数组
        $notice    = new NoticeModel();
        $condition = [
            ['receiving_type', 'like', '%mobile%'],
            ['site_id', '=', $this->site_id],
            ['id', 'in', $id_arr]
        ];
        $list      = $notice->getNoticeList($condition);
        return $this->response($list);
    }
    public function page()
    {
        $page      = isset($this->params['page']) ? $this->params['page'] : 1;
        $page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
        $notice    = new NoticeModel();
        $list      = $notice->getNoticePageList([['receiving_type', 'like', '%mobile%'], ['site_id', '=', $this->site_id]], $page, $page_size);
        return $this->response($list);
    }
}