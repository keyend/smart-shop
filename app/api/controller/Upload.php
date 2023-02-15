<?php
namespace app\api\controller;
use app\model\upload\Upload as UploadModel;
/**
 * 上传管理
 * @author Administrator
 *
 */
class Upload extends BaseApi
{
    /**
     * 头像上传
     */
    public function headimg()
    {
        $upload_model = new UploadModel($this->site_id);
        $param        = array(
            "thumb_type" => "",
            "name"       => "file",
            "watermark" => 0,
            "cloud" => 1
        );
        $result       = $upload_model->setPath("headimg/" . date("Ymd") . '/')->image($param);
        return $this->response($result);
    }
    /**
     * 评价上传
     */
    public function evaluateimg()
    {
        $upload_model = new UploadModel($this->site_id);
        $param        = array(
            "thumb_type" => "",
            "name"       => "file",
            "watermark" => 0,
            "cloud" => 1
        );
        $result       = $upload_model->setPath("evaluate_img/" . date("Ymd") . '/')->image($param);
        return $this->response($result);
    }

    public function headimgBase64()
    {
        $upload_model = new UploadModel($this->site_id);
        $file = input('images', '');
        $file = base64_to_blob($file);
        $result       = $upload_model->setPath("headimg/" . date("Ymd") . '/')->remotePullBinary($file['blob']);
        return $this->response($result);
    }

    /**
     * 平台客服图片上传
     *
     * @return void
     */
    public function chatimg()
    {
        $upload_model = new UploadModel($this->site_id);
        $param        = array(
            "thumb_type" => "",
            "name"       => "file",
            "watermark" => 0,
            "cloud" => 1
        );
        $result       = $upload_model->setPath("chatimg/" . date("Ymd") . '/')->image($param);
        return $this->response($result);
    }
}