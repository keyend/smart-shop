<?php
namespace addon\store\component\controller;
use app\component\controller\BaseDiyView;
/**
 * 门店信息·组件
 *
 */
class StoreInfo extends BaseDiyView
{
    /**
     * 设计界面
     */
    public function design()
    {
        return $this->fetch("store_info/design.html");
    }
}