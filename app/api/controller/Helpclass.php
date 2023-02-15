<?php
namespace app\api\controller;
use app\model\web\Help as HelpModel;
class Helpclass extends BaseApi
{
    /**
     * 列表信息
     */
    public function lists()
    {
        $help      = new HelpModel();
        $condition = [
            ['site_id', '=', $this->site_id]
        ];
        $list      = $help->getHelpClassList($condition, 'class_id, class_name', 'sort desc');
        $order     = 'create_time desc';
        $field     = 'id,title,link_address';
        if (!empty($list['data'])) {
            foreach ($list['data'] as $k => $v) {
                $condition                      = [
                    ['class_id', '=', $v['class_id']],
                    ['site_id', '=', $this->site_id]
                ];
                $child_list                     = $help->getHelpList($condition, $field, $order);
                $child_list                     = $child_list['data'];
                $list['data'][$k]['child_list'] = $child_list;
            }
        }
        return $this->response($list);
    }
}