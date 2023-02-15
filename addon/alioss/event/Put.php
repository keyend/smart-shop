<?php
namespace addon\alioss\event;
use addon\alioss\model\Alioss;
/**
 * 云上传方式
 */
class Put
{
    /**
     * 短信发送方式方式及配置
     */
    public function handle($param)
    {
        $qiniu_model = new Alioss();
        $result      = $qiniu_model->putFile($param);
        return $result;
    }
}