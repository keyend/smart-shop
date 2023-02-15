<?php
namespace addon\system\Wechat\event;
use liliuwei\think\Jump;
class DoEditMessage
{
    use Jump;
    /**
     * 编辑消息模板
     * @param array $param
     */
    public function handle($param = [])
    {
        if ($param["name"] == "Wechat") {
            $this->redirect(addon_url('Wechat://sitehome/message/edit', ['keyword' => $param['keyword']]));
        }
    }
}