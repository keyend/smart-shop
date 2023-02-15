<?php
namespace app\shop\controller;
use app\model\store\Store as StoreModel;
use app\model\system\Address as AddressModel;
use app\model\web\Config as ConfigModel;
/**
 * 门店
 * Class Store
 * @package app\shop\controller
 */
class Store extends BaseShop
{
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
    }
    /**
     * 门店列表
     * @return mixed
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $store_model = new StoreModel();
            $page        = input('page', 1);
            $page_size   = input('page_size', PAGE_LIST_ROWS);
            $order       = input("order", "create_time desc");
            $keyword     = input("keyword", '');
            $status      = input("status", '');
            $condition   = [];
            $condition[] = ['site_id', "=", $this->site_id];
            if ($status != null) {
                $condition[] = ['status', '=', $status];
            }
            //关键字查询
            if (!empty($keyword)) {
                $condition[] = ["store_name", "like", "%" . $keyword . "%"];
            }
            $list = $store_model->getStorePageList($condition, $page, $page_size, $order);
            return $list;
        } else {
            //判断门店插件是否存在
            $store_is_exit = addon_is_exit('store', $this->site_id);
            $this->assign('store_is_exit', $store_is_exit);
            return $this->fetch("store/lists");
        }
    }
    /**
     * 添加门店
     * @return mixed
     */
    public function addStore()
    {
        $is_store = addon_is_exit('store');
        if (request()->isAjax()) {
            $store_name   = input("store_name", '');
            $type         = input("type", 0);
            $telphone     = input("telphone", '');
            $store_image  = input("store_image", '');
            $status       = input("status", 0);
            $province_id  = input("province_id", 0);
            $city_id      = input("city_id", 0);
            $district_id  = input("district_id", 0);
            $community_id = input("community_id", 0);
            $address      = input("address", '');
            $full_address = input("full_address", '');
            $longitude    = input("longitude", 0);
            $latitude     = input("latitude", 0);
            $is_pickup    = input("is_pickup", 0);
            $is_custom    = input("is_custom", 0);
            $is_recomment = input("is_recomment", 0);
            $is_o2o       = input("is_o2o", 0);
            $open_date    = input("open_date", '');
            $extra        = input("extra", []);
            $data         = array(
                "store_name"   => $store_name,
                "type"         => $type,
                "telphone"     => $telphone,
                "store_image"  => $store_image,
                "status"       => $status,
                "province_id"  => $province_id,
                "city_id"      => $city_id,
                "district_id"  => $district_id,
                "community_id" => $community_id,
                "address"      => $address,
                "full_address" => $full_address,
                "longitude"    => $longitude,
                "latitude"     => $latitude,
                "is_pickup"    => $is_pickup,
                "is_custom"    => $is_custom,
                "is_recomment" => $is_recomment,
                "is_o2o"       => $is_o2o,
                "open_date"    => $open_date,
                "site_id"      => $this->site_id,
                "extra"        => json_encode($extra)
            );
            //判断是否开启多门店
            if ($is_store == 1) {
                $user_data = [ 'username' => input('username', '') ];
            } else {
                $user_data = [];
            }
            $store_model = new StoreModel();
            $result      = $store_model->addStore($data, $user_data, $is_store);
            return $result;
        } else {
            //查询省级数据列表
            $address_model = new AddressModel();
            $list          = $address_model->getAreaList([["pid", "=", 0], ["level", "=", 1]]);
            $this->assign("province_list", $list["data"]);
            $this->assign("is_exit", $is_store);
            $this->assign("http_type", get_http_type());
			
            $config_model = new ConfigModel();
            $mp_config = $config_model->getMapConfig($this->site_id);
            $this->assign('tencent_map_key', $mp_config[ 'data' ][ 'value' ][ 'tencent_map_key' ]);
			
            return $this->fetch("store/add_store");
        }
    }
    /**
     * 编辑门店
     * @return mixed
     */
    public function editStore()
    {
        $store_id    = input("store_id", 0);
        $condition   = array(
            ["site_id", "=", $this->site_id],
            ["store_id", "=", $store_id]
        );
        $store_model = new StoreModel();
        if (request()->isAjax()) {
            $store_name   = input("store_name", '');
            $type         = input("type", 0);
            $telphone     = input("telphone", '');
            $store_image  = input("store_image", '');
            $status       = input("status", 0);
            $province_id  = input("province_id", 0);
            $city_id      = input("city_id", 0);
            $district_id  = input("district_id", 0);
            $community_id = input("community_id", 0);
            $address      = input("address", '');
            $full_address = input("full_address", '');
            $longitude    = input("longitude", 0);
            $latitude     = input("latitude", 0);
            $is_pickup    = input("is_pickup", 0);
            $is_custom    = input("is_custom", 0);
            $is_recomment = input("is_recomment", 0);
            $is_o2o       = input("is_o2o", 0);
            $open_date    = input("open_date", '');
            $extra        = input("extra", []);
            $data         = array(
                "store_name"   => $store_name,
                "type"         => $type,
                "telphone"     => $telphone,
                "store_image"  => $store_image,
                "status"       => $status,
                "province_id"  => $province_id,
                "city_id"      => $city_id,
                "district_id"  => $district_id,
                "community_id" => $community_id,
                "address"      => $address,
                "full_address" => $full_address,
                "longitude"    => $longitude,
                "latitude"     => $latitude,
                "is_pickup"    => $is_pickup,
                "is_custom"    => $is_custom,
                "is_recomment" => $is_recomment,
                "is_o2o"       => $is_o2o,
                "open_date"    => $open_date,
                "extra"        => json_encode($extra)
            );
            $result       = $store_model->editStore($data, $condition);
            return $result;
        } else {
            //查询省级数据列表
            $address_model = new AddressModel();
            $list          = $address_model->getAreaList([["pid", "=", 0], ["level", "=", 1]]);
            $this->assign("province_list", $list["data"]);
            $info_result = $store_model->getStoreInfo($condition);//门店信息
            $info        = $info_result["data"];
			
            $this->assign("info", $info);
            $this->assign("store_id", $store_id);
            $is_exit = addon_is_exit("store");
            $this->assign("is_exit", $is_exit);
            $this->assign("http_type", get_http_type());
			
            $config_model = new ConfigModel();
            $mp_config = $config_model->getMapConfig($this->site_id);
            $this->assign('tencent_map_key', $mp_config[ 'data' ][ 'value' ][ 'tencent_map_key' ]);		
			
            return $this->fetch("store/edit_store");
        }
    }
    /**
     * 删除门店
     * @return mixed
     */
    public function deleteStore()
    {
        if (request()->isAjax()) {
            $store_id    = input("store_id", 0);
            $condition   = array(
                ["site_id", "=", $this->site_id],
                ["store_id", "=", $store_id]
            );
            $store_model = new StoreModel();
            $result      = $store_model->deleteStore($condition);
            return $result;
        }
    }
    public function frozenStore()
    {
        if (request()->isAjax()) {
            $store_id    = input('store_id', 0);
            $is_frozen   = input('is_frozen', 0);
            $condition   = [
                ["site_id", "=", $this->site_id],
                ["store_id", "=", $store_id]
            ];
            $store_model = new StoreModel();
            $res         = $store_model->frozenStore($condition, $is_frozen);
            return $res;
        }
    }
    /**
     * 重置密码
     */
    public function modifyPassword()
    {
        if(request()->isAjax()){
            $store_id = input('store_id', '');
            $password = input('password', '123456');
            $store_model = new StoreModel();
            return $store_model->resetStorePassword($password, [['store_id', '=', $store_id]]);
        }
    }
}