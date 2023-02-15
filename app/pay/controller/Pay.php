<?php
namespace app\pay\controller;
use app\Controller;
use think\facade\Log;
/**
 * 支付控制器
 */
class Pay extends Controller
{
    /**
     * 支付异步回调
     */
    public function notify()
    {
        $param = input();
        event('PayNotify', []);
    }
    public function payReturn()
    {
    }
}