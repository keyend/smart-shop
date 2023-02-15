<?php
namespace addon\topic\api\controller;
use app\api\controller\BaseApi;
use addon\topic\model\Topic as TopicModel;
/**
 * 专题活动
 */
class Topic extends BaseApi
{
    /**
     * 列表信息
     */
    public function page()
    {
        $page        = isset($this->params['page']) ? $this->params['page'] : 1;
        $page_size   = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
        $condition   = [
            ['status', '=', 2],
            ['site_id', '=', $this->site_id]
        ];
        $order       = 'modify_time desc,create_time desc';
        $field       = 'topic_id,topic_name,topic_adv,status,remark,start_time,end_time';
        $topic_model = new TopicModel();
        $list        = $topic_model->getTopicPageList($condition, $page, $page_size, $order, $field);
        return $this->response($list);
    }
}