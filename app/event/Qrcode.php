<?php
namespace app\event;
use app\model\system\Qrcode as QrcodeModel;
/**
 * 生成二维码
 * @author Administrator
 *
 */
class Qrcode
{
    public function handle($param)
    {
        if (in_array($param["app_type"], ['h5', 'all', 'wechat'])) {
            $param["app_type"] = 'h5';
            $qrcode            = new QrcodeModel();
            $res               = $qrcode->createQrcode($param);
            return $res;
        }
    }
}