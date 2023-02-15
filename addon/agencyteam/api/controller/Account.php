<?php
namespace addon\fenxiao\api\controller;
use app\api\controller\BaseApi;

class Account extends BaseApi
{
    public function page()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        return $this->response($this->error('', 'FENXIAO_NOT_EXIST'));
    }

}