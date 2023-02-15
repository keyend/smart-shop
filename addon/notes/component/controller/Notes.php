<?php
namespace addon\notes\component\controller;
use app\component\controller\BaseDiyView;
/**
 * 店铺笔记·组件
 *
 */
class Notes extends BaseDiyView
{
    /**
     * 设计界面
     */
    public function design()
    {
        return $this->fetch("notes/design.html");
    }
}