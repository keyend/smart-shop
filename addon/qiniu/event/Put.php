<?php
namespace addon\qiniu\event;
use addon\qiniu\model\Qiniu;
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
        $qiniu_model = new Qiniu();
        $result      = $qiniu_model->putFile($param);
        return $result;
    }
}