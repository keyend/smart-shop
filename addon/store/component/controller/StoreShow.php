<?php
namespace addon\store\component\controller;
use app\component\controller\BaseDiyView;
/**
 * 门店展示·组件
 *
 */
class StoreShow extends BaseDiyView
{
    /**
     * 设计界面
     */
    public function design()
    {
        return $this->fetch("store_show/design.html");
    }
}