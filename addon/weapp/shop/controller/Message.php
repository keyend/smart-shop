<?php
namespace addon\weapp\shop\controller;
use app\shop\controller\BaseShop;
use addon\weapp\model\Message as MessageModel;
use app\model\message\Message as MessageSyetem;
/**
 * 微信小程序订阅消息
 */
class Message extends BaseShop
{
    /**
     * 模板消息设置
     * @return array|mixed|\multitype
     */
    public function config()
    {
        $message_model = new MessageModel();
        if (request()->isAjax()) {
            $page      = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $condition = array(
                ["support_type", "like", '%weapp%'],
            );
            $list      = $message_model->getMessagePageList($condition, $this->site_id, $page, $page_size);
            return $list;
        } else {
            return $this->fetch('message/config');
        }
    }
    /**
     * 微信模板消息状态设置
     */
    public function setWeappStatus()
    {
        $message_model = new MessageModel();
        if (request()->isAjax()) {
            $keywords       = input("keywords", "");
            $weapp_is_open = input('weapp_is_open', 0);
            $res = $message_model->getWeappTemplateNo($keywords, $this->site_id, $weapp_is_open);
            return $res;
        }
    }
    /**
     * 获取模板编号
     */
    public function getWeappTemplateNo()
    {
        if (request()->isAjax()) {
            $keywords      = input("keywords", "");
            $message_model = new MessageModel();
            $res           = $message_model->getWeappTemplateNo($keywords, $this->site_id, -1);
            return $res;
        }
    }
    /**
     * 编辑模板消息
     * @return array|mixed|string
     */
    public function edit()
    {
        $message_model = new MessageSyetem();
        $keywords      = input("keywords", "");
        $info_result   = $message_model->getMessageInfo($this->site_id, $keywords);
        $info          = $info_result["data"];
        $weapp_json_array = $info["weapp_json_array"];
        if (request()->isAjax()) {
            if (empty($info))
                return error("", "不存在的模板信息!");
            $weapp_is_open                       = input('weapp_is_open', 0);
            $res = $message_model->editMessage(['weapp_is_open' => $weapp_is_open, 'site_id' => $this->site_id, 'keywords' => $keywords], [
                ["keywords", "=", $keywords],
                ['site_id', '=', $this->site_id],
            ]);
            return $res;
        } else {
            if (empty($info))
                $this->error("不存在的模板信息!");
            $this->assign("keywords", $keywords);
            $this->assign("info", $weapp_json_array);
            $this->assign('weapp_is_open', $info['weapp_is_open']);
            return $this->fetch('message/edit');
        }
    }
}