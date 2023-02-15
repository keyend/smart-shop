<?php
namespace addon\store\model;
use app\model\BaseModel;
/**
 * 菜单管理
 * @author Administrator
 *
 */
class Menu extends BaseModel
{
    public function uninstall()
    {
        $res = model("menu")->delete([['app_module', '=', 'store']]);
        return $this->success($res);
    }
}