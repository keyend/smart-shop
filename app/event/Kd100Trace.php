<?php
namespace app\event;
use app\model\express\Kd100;
/**
 * 初始化配置信息
 * @author Administrator
 *
 */
class Kd100Trace
{
    public function handle($data)
    {
        $express_no_data = $data["express_no_data"];
        $kd100_model   = new Kd100();
        $config_result = $kd100_model->getKd100Config($express_no_data["site_id"]);
        $config        = $config_result["data"];
        if ($config["is_use"]) {
            $express_no = $express_no_data["express_no_kd100"];
            $result     = $kd100_model->trace($data["code"], $express_no, $express_no_data["site_id"]);
            return $result;
        }
    }
}