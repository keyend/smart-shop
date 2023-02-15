<?php
namespace addon\ttapp\api\controller;
use addon\ttapp\model\Ttapp;
use app\Controller;
use think\facade\Log;

class Auth extends Controller
{
    public $ttapp;
    public function __construct()
    {
        parent::__construct();
        $site_id      = request()->siteid();
        $this->ttapp  = new Weapp($site_id);
    }
}