<?php
namespace addon\alisms\shop\controller;
use app\model\message\Message as MessageModel;
use app\model\message\MessageTemplate as MessageTemplateModel;
use app\shop\controller\BaseShop;
/**
 * 阿里云短信消息管理
 */
class Message extends BaseShop
{
    /**
     * 编辑模板消息
     * @return array|mixed|string
     */
    public function edit()
    {
        $message_model = new MessageModel();
        $keywords      = input("keywords", "");
        $info_result   = $message_model->getMessageInfo($this->site_id, $keywords);
        $info          = $info_result["data"];
        if (request()->isAjax()) {
            if (empty($info))
                return error("", "不存在的模板信息!");
            $sms_is_open    = input('sms_is_open', 0);
            $sms_json_array = !empty($info["sms_json_array"]) ? $info["sms_json_array"] : [];//短信配置
            $template_id    = input("template_id", '');//短信模板id
            $smssign        = input("smssign", '');//短信签名
            $content        = input("content", '');//短信签名
            $ali_array = [];
            if (!empty($sms_json_array["alisms"])) {
                $ali_array = $sms_json_array["alisms"];
            }
            $ali_array['template_id'] = $template_id;//模板ID  (备注:服务商提供的模板ID)
            $ali_array['content']     = $content;//模板内容 (备注:仅用于显示)
            $ali_array['smssign']     = $smssign;//短信签名  (备注:请填写短信签名(如果服务商是大于请填写审核成功的签名))
            $sms_json_array["alisms"] = $ali_array;
            $data                     = array(
                'sms_json' => json_encode($sms_json_array),
            );
            $condition                = array(
                ["keywords", "=", $keywords]
            );
            $template_model           = new MessageTemplateModel();
            $res                      = $template_model->editMessageTemplate($data, $condition);
            if ($res['code'] == 0) {
                $res = $message_model->editMessage(['sms_is_open' => $sms_is_open, 'site_id' => $this->site_id, 'keywords' => $keywords], [
                    ["keywords", "=", $keywords],
                    ['site_id', '=', $this->site_id],
                ]);
            }
            return $res;
        } else {
            if (empty($info))
                $this->error("不存在的模板信息!");
            $sms_json_array = $info["sms_json_array"];//短信配置
            $ali_array      = [];
            if (!empty($sms_json_array["alisms"])) {
                $ali_array = $sms_json_array["alisms"];
            }
            $this->assign("info", $ali_array);
            $this->assign("keywords", $keywords);
            //模板变量
            $message_variable_list = $info["message_json_array"];
            $this->assign("message_variable_list", $message_variable_list);
            $this->assign('sms_is_open', $info['sms_is_open']);
            return $this->fetch('message/edit');
        }
    }
}