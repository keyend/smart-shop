<?php
namespace app\event;
use app\model\express\Kdbird;
/**
 * 初始化配置信息
 * @author Administrator
 *
 */
class KdbirdTrace
{
    public function handle($data)
    {
        $express_no_data = $data["express_no_data"];
        $kdbird_model  = new Kdbird();
        $config_result = $kdbird_model->getKdbirdConfig($express_no_data["site_id"]);
        $config        = $config_result["data"];
        if ($config["is_use"]) {
            $express_no = $express_no_data["express_no"];
            $result     = $kdbird_model->trace($data["code"], $express_no, $express_no_data["site_id"]);
            return $result;
        }
    }
}