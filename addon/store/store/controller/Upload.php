<?php
namespace addon\store\store\controller;
use app\model\upload\Upload as UploadModel;
/**
 * 图片上传
 * Class Verify
 * @package app\shop\controller
 */
class Upload extends BaseStore
{
    public $site_id = 0;
    protected $app_module = "store";
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
    }
    /**
     * 上传文件
     */
    public function file()
    {
        $upload_model = new UploadModel($this->site_id);
        $param = array (
            "name" => "file",
            'extend_type' => [ 'xlsx' ]
        );
        $result = $upload_model->setPath("common/store/file/" . date("Ymd") . '/')->file($param);
        return $result;
    }
    /**
     * 删除文件
     */
    public function deleteFile()
    {
        if (request()->isAjax()) {
            $path = input("path", '');
            $res = false;
            if (!empty($path)) {
                $res = delFile($path);
            }
            return $res;
        }
    }
    /**
     * 上传(不存入相册)
     * @return \app\model\upload\Ambigous|\multitype
     */
    public function image()
    {
        $upload_model = new UploadModel($this->site_id, $this->app_module);
        $thumb_type = input("thumb", "");
        $name = input("name", "");
        $watermark = input("watermark", 0); // 是否需生成水印
        $cloud = input("cloud", 1); // 是否需上传到云存储
        $param = array (
            "thumb_type" => "",
            "name" => "file",
            "watermark" => $watermark,
            "cloud" => $cloud
        );
        $path = $this->site_id > 0 ? "common/images/" . date("Ymd") . '/' : "common/images/" . date("Ymd") . '/';
        $result = $upload_model->setPath($path)->image($param);
        return $result;
    }
}