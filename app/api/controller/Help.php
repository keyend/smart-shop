<?php
namespace app\api\controller;
use app\model\web\Help as HelpModel;
/**
 * 系统帮助
 * @author Administrator
 *
 */
class Help extends BaseApi
{
    /**
     * 基础信息
     */
    public function info()
    {
        $help_id = isset($this->params['id']) ? $this->params['id'] : 0;
        if (empty($help_id)) {
            return $this->response($this->error('', 'REQUEST_ID'));
        }
        $help = new HelpModel();
        $info = $help->getHelpInfo($help_id);
        return $this->response($info);
    }
    /**
     * 分页列表信息
     */
    public function page()
    {
        $page      = isset($this->params['page']) ? $this->params['page'] : 1;
        $page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
        $class_id  = isset($this->params['class_id']) ? $this->params['class_id'] : 0;
        $condition = [
            ['class_id', '=', $class_id],
            ['site_id', '=', $this->site_id]
        ];
        $order     = 'create_time desc';
        $field     = 'id,title,class_id,class_name,sort,create_time';
        $help      = new HelpModel();
        $list      = $help->getHelpPageList($condition, $page, $page_size, $order, $field);
        return $this->response($list);
    }
}