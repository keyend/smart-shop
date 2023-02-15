<?php
namespace app\api\controller;
use app\model\system\Address as AddressModel;
/**
 * 地址管理
 * @author Administrator
 *
 */
class Address extends BaseApi
{
    /**
     * 基础信息
     */
    public function info()
    {
        $id      = $this->params['id'];
        $address = new AddressModel();
        $info    = $address->getAreaInfo($id);
        return $this->response($info);
    }
    /**
     * 列表信息
     */
    public function lists()
    {
        $pid     = isset($this->params['pid']) ? $this->params['pid'] : 0;
        $address = new AddressModel();
        $list    = $address->getAreas($pid);
        return $this->response($list);
    }
    /**
     * 树状结构信息
     */
    public function tree()
    {
        $id      = $this->params['id'];
        $address = new AddressModel();
        $tree    = $address->getAreas($id);
        return $this->response($tree);
    }
}