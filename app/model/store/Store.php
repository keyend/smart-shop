<?php
namespace app\model\store;
use app\model\BaseModel;
use app\model\system\Group;
use think\facade\Cache;
use think\facade\Db;
/**
 * 门店管理
 */
class Store extends BaseModel
{
    /**
     * 添加门店
     * @param unknown $data
     */
    public function addStore($data, $user_data = [], $is_store = 0)
    {
        $site_id = isset($data['site_id']) ? $data['site_id'] : '';
        if ($site_id === '') {
            return $this->error('', 'REQUEST_SITE_ID');
        }
        if (empty($data['longitude']) || empty($data['latitude'])) {
            return $this->error('', '门店经纬度不能为空');
        }
        $data['create_time'] = time();
        model('store')->startTrans();
        try {
            if ($is_store == 1) {
                $data['username'] = $user_data['username'];
                $store_id = model('store')->add($data);
                Cache::tag("store")->clear();
                //添加系统用户组
                $group = new Group();
                $group_data = [
                    'site_id' => $store_id,
                    'app_module' => 'store',
                    'group_name' => '管理员组',
                    'is_system' => 1,
                    'create_time' => time()
                ];
                $group_id = $group->addGroup($group_data)['data'];
                //用户检测
                if (empty($user_data['username'])) {
                    model("store")->rollback();
                    return $this->error('', 'USER_NOT_EXIST');
                }

                // 从用户表查找判断
                $member_info = modal("member")->getInfo([['username', '=', $user_data['username']], ['site_id', '=', $site_id]], "member_id,username,password");
                if (empty($member_info)) {
                    model("store")->rollback();
                    return $this->error('无此用户', 'USER_NOT_EXIST');
                }
                $user_data['member_id'] = $member_info['member_id'];
                $user_data['password'] = $member_info['password'];

                // 已存在的用户
                $user_count = model("user")->getCount([['username', '=', $user_data['username']], ['app_module', '=', 'store'], ['site_id', '=', $site_id]]);
                if ($user_count > 0) {
                    model("store")->rollback();
                    return $this->error('', 'USERNAME_EXISTED');
                }
                //添加用户
                $data_user = [
                    'app_module' => 'store',
                    'app_group' => 0,
                    'is_admin' => 1,
                    'group_id' => $group_id,
                    'group_name' => '管理员组',
                    'site_id' => $data['site_id']
                ];
                $user_info = array_merge($data_user, $user_data);
                $uid = model("user")->add($user_info);
                model('store')->update(['uid' => $uid], [['store_id', '=', $store_id]]);
                //执行事件
                event("AddStore", ['store_id' => $store_id, 'site_id' => $data['site_id']]);
            } else {
                $store_id = model('store')->add($data);
                Cache::tag("store")->clear();
            }
            model('store')->commit();
            return $this->success($store_id);
        } catch (\Exception $e) {
            model('store')->rollback();
            return $this->error('', $e->getMessage());
        }
    }
    /**
     * 修改门店
     * @param unknown $data
     * @return multitype:string
     */
    public function editStore($data, $condition)
    {
        if (empty($data['longitude']) || empty($data['latitude'])) {
            return $this->error('', '门店经纬度不能为空');
        }
        $check_condition = array_column($condition, 2, 0);
        $site_id = isset($check_condition['site_id']) ? $check_condition['site_id'] : '';
        if ($site_id === '') {
            return $this->error('', 'REQUEST_SITE_ID');
        }
        $data["modify_time"] = time();
        $res = model('store')->update($data, $condition);
        Cache::tag("store")->clear();
        return $this->success($res);
    }
    /**
     * 删除门店
     * @param unknown $condition
     */
    public function deleteStore($condition)
    {
        $check_condition = array_column($condition, 2, 0);
        $site_id = isset($check_condition['site_id']) ? $check_condition['site_id'] : '';
        $store_id = isset($check_condition['store_id']) ? $check_condition['store_id'] : '';
        if ($site_id === '') {
            return $this->error('', 'REQUEST_SITE_ID');
        }
        $store_info = model('store')->getInfo([['store_id', '=', $store_id]], 'uid');
        $res = model('store')->delete($condition);
        if ($res) {
            model('store_goods')->delete([['store_id', '=', $store_id]]);
            model('store_goods_sku')->delete([['store_id', '=', $store_id]]);
            model('store_member')->delete([['store_id', '=', $store_id]]);
            model('store_settlement')->delete([['store_id', '=', $store_id], ['site_id', '=', $site_id]]);
            model('user')->delete([['app_module', '=', 'store'], ['site_id', '=', $site_id], ['uid', '=', $store_info['uid']]]);
            model('site_diy_view')->delete([['name', '=', 'DIY_STORE_' . $store_id], ['site_id', '=', $site_id]]);
        }
        Cache::tag("store")->clear();
        return $this->success($res);
    }
    /**
     * @param $condition
     * @param $is_frozen
     */
    public function frozenStore($condition, $is_frozen)
    {
        $check_condition = array_column($condition, 2, 0);
        $site_id = isset($check_condition['site_id']) ? $check_condition['site_id'] : '';
        if ($site_id === '') {
            return $this->error('', 'REQUEST_SITE_ID');
        }
        $res = model('store')->update(['is_frozen' => $is_frozen == 1 ? 0 : 1], $condition);
        Cache::tag("store")->clear();
        return $this->success($res);
    }
    /**
     * 重置密码
     * @param string $password
     * @param $condition
     * @return array
     */
    public function resetStorePassword($password = '123456', $condition)
    {
        //获取用户id
        $uid = model('store')->getValue($condition,'uid');
        if($uid){
            $res = model('user')->update([
                'password' => data_md5($password)
            ], [['uid','=',$uid]]);
        }else{
            $res = 1;
        }
        if ($res === false) {
            return $this->error('', 'RESULT_ERROR');
        }
        return $this->success($res);
    }
    /**
     * 获取门店信息
     * @param array $condition
     * @param string $field
     */
    public function getStoreInfo($condition, $field = '*')
    {
        $data = json_encode([$condition, $field]);
        $cache = Cache::get("store_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $res = model('store')->getInfo($condition, $field);
        if (!empty($res)) {
            if (!empty($res['extra'])) {
                $res['extra'] = json_decode($res['extra']);
            }
        }
        Cache::tag("store")->set("store_" . $data, $res);
        return $this->success($res);
    }
    /**
     * 获取门店列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getStoreList($condition = [], $field = '*', $order = '', $limit = null)
    {
        $data = json_encode([$condition, $field, $order, $limit]);
        $cache = Cache::get("store_getStoreList_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $list = model('store')->getList($condition, $field, $order, '', '', '', $limit);
        Cache::tag("store")->set("store_getStoreList_" . $data, $list);
        return $this->success($list);
    }
    /**
     * 获取门店分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getStorePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $data = json_encode([$condition, $field, $order, $page, $page_size]);
        $cache = Cache::get("store_getStorePageList_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $list = model('store')->pageList($condition, $field, $order, $page, $page_size);
        Cache::tag("store")->set("store_getStorePageList_" . $data, $list);
        return $this->success($list);
    }
    /**
     * 查询门店  带有距离
     * @param $condition
     * @param $lnglat
     */
    public function getLocationStoreList($condition, $field, $lnglat)
    {
        $order = '';
        if ($lnglat['lat'] !== null && $lnglat['lng'] !== null) {
            $field .= ' , ROUND(st_distance ( point ( ' . $lnglat['lng'] . ', ' . $lnglat['lat'] . ' ), point ( longitude, latitude ) ) * 111195 / 1000, 2) as distance ';
            $condition[] = ['', 'exp', Db::raw(' FORMAT(st_distance ( point ( ' . $lnglat['lng'] . ', ' . $lnglat['lat'] . ' ), point ( longitude, latitude ) ) * 111195 / 1000, 2) < 10000')];
            $order = 'distance asc';
        }
        $list = model('store')->getList($condition, $field, $order);
        return $this->success($list);
    }
    /**
     * 查询门店  带有距离
     * @param $condition
     * @param $lnglat
     */
    public function getLocationStorePageList($condition, $page = 1, $page_size = PAGE_LIST_ROWS, $field, $lnglat)
    {
        $order = '';
        if ($lnglat['lat'] !== null && $lnglat['lng'] !== null) {
            $field .= ',FORMAT(st_distance ( point ( ' . $lnglat['lng'] . ', ' . $lnglat['lat'] . ' ), point ( longitude, latitude ) ) * 111195 / 1000, 2) as distance';
            $condition[] = ['', 'exp', Db::raw(' FORMAT(st_distance ( point ( ' . $lnglat['lng'] . ', ' . $lnglat['lat'] . ' ), point ( longitude, latitude ) ) * 111195 / 1000, 2) < 10000')];
            $order = Db::raw(' st_distance ( point ( ' . $lnglat['lng'] . ', ' . $lnglat['lat'] . ' ), point ( longitude, latitude ) ) * 111195 / 1000 asc');
        }
        $list = model('store')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }
}