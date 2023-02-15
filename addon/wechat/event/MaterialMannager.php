<?php
namespace addon\system\Wechat\event;
use addon\system\Wechat\sitehome\controller\Material;
use think\facade\View;
class MaterialMannager
{
    /**
     * 图文消息管理
     */
    public function handle($param = [])
    {
        $material     = new Material();
        $result       = $material->materialMannager();
        $return_array = array_merge($result[1], $param);
        return View::fetch($result[0], $return_array, $result[2]);
    }
}