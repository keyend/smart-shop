<?php
namespace app\model\system;
use app\model\BaseModel;
use extend\QRcode as QRcodeExtend;
/**
 * 二维码生成类
 */
class Qrcode extends BaseModel
{
    public function createQrcode(array $param)
    {
        try {
            $checkpath_result = $this->checkPath($param['qrcode_path']);
            if ($checkpath_result["code"] != 0) return $checkpath_result;
            $urlParam = '';
            if (!empty($param['data'])) {
                foreach ($param['data'] as $key => $value) {
                    if ($urlParam == '') $urlParam .= '?' . $key . '=' . $value;
                    else $urlParam .= '&' . $key . '=' . $value;
                }
            }
            $domain   = getH5Domain();
            $url      = $domain . $param['page'] . $urlParam;
            $filename = $param['qrcode_path'] . '/' . $param['qrcode_name'] . '_' . $param['app_type'] . '.png';
            QRcodeExtend::png($url, $filename, 'L', 4, 1);
            return $this->success(['path' => $filename]);
        } catch (\Exception $e) {
            return $this->error('', $e->getMessage());
        }
    }
    /**
     * 校验目录是否可写
     * @param unknown $path
     * @return multitype:number unknown |multitype:unknown
     */
    private function checkPath($path)
    {
        if (is_dir($path) || mkdir($path, intval('0755', 8), true)) {
            return $this->success();
        }
        return $this->error('', "directory {$path} creation failed");
    }
}