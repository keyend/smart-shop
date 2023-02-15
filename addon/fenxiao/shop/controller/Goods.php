<?php
namespace addon\fenxiao\shop\controller;
use addon\fenxiao\model\FenxiaoGoodsSku as FenxiaoGoodsSkuModel;
use addon\fenxiao\model\FenxiaoLevel as FenxiaoLevelModel;
use app\model\goods\Goods as GoodsModel;
use app\shop\controller\BaseShop;
use addon\fenxiao\model\FenxiaoGoods as FenxiaoGoodsModel;
use think\facade\Db;
use addon\fenxiao\model\Config as FenxiaoConfigModel;
/**
 *  分销商品
 */
class Goods extends BaseShop
{
    /**
     * 分销等级列表
     */
    public function lists()
    {
        $model = new GoodsModel();
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $condition = [ [ 'is_delete', '=', 0 ], [ 'site_id', '=', $this->site_id ] ];
            $search_text_type = input('search_text_type', "goods_name");//店铺名称或者商品名称
            $search_text = input('search_text', "");
            $goods_class = input('goods_class', "");//商品种类
            $goods_state = input('goods_state', "");//商品状态
            $category_id = input('category_id', "");//分类ID
            $is_fenxiao = input('is_fenxiao', "");
            $start_sale = input('start_sale', 0);
            $end_sale = input('end_sale', 0);
            if (!empty($search_text)) {
                $condition[] = [ $search_text_type, 'like', '%' . $search_text . '%' ];
            }
            if ($is_fenxiao !== "") {
                $condition[] = [ 'is_fenxiao', '=', $is_fenxiao ];
            }
            if (!empty($start_sale)) $condition[] = [ 'sale_num', '>=', $start_sale ];
            if (!empty($end_sale)) $condition[] = [ 'sale_num', '<=', $end_sale ];
            if ($goods_class !== "") {
                $condition[] = [ 'goods_class', '=', $goods_class ];
            }
            if ($goods_state !== '') {
                $condition[] = [ 'goods_state', '=', $goods_state ];
            }
            if (!empty($category_id)) {
                $condition[] = [ 'category_id', 'like', '%,' . $category_id . ',%' ];
            }
            $list = $model->getGoodsPageList($condition, $page_index, $page_size);
            return $list;
        } else {
            return $this->fetch('goods/lists');
        }
    }
    public function detail()
    {
        $goods_id = input('goods_id');
        $goods_model = new GoodsModel();
        $fenxiao_sku_model = new FenxiaoGoodsSkuModel();
        $fenxiao_leve_model = new FenxiaoLevelModel();
        $goods_info = $goods_model->getGoodsDetail($goods_id);
        if (empty($goods_info[ 'data' ]) || $goods_info[ 'data' ][ 'site_id' ] != $this->site_id) return $this->error('商品信息不存在');
        $fenxiao_skus = $fenxiao_sku_model->getSkuList([ 'goods_id' => $goods_id ]);
        $skus = [];
        foreach ($fenxiao_skus[ 'data' ] as $fenxiao_sku) {
            $skus[ $fenxiao_sku[ 'level_id' ] . '_' . $fenxiao_sku[ 'sku_id' ] ] = $fenxiao_sku;
        }
        $goods_info[ 'data' ][ 'fenxiao_skus' ] = $skus;
        $goods_info[ 'data' ][ 'goods_image' ] = explode(',', $goods_info[ 'data' ][ 'goods_image' ]);
        $fenxiao_level = $fenxiao_leve_model->getLevelList([ [ 'site_id', '=', $this->site_id ] ]);
        $this->assign('fenxiao_level', $fenxiao_level[ 'data' ]);
        $this->assign('goods_info', $goods_info[ 'data' ]);
        $fenxiao_config_model = new FenxiaoConfigModel();
        $fenxiao_config = $fenxiao_config_model->getFenxiaoBasicsConfig($this->site_id)['data'] ?? [];
        $this->assign('fenxiao_config', $fenxiao_config['value'] ?? []);
        return $this->fetch('goods/detail');
    }
    /**
     * 添加活动
     */
    public function config()
    {
        $goods_id = input('goods_id');
        $goods_model = new GoodsModel();
        $fenxiao_sku_model = new FenxiaoGoodsSkuModel();
        $fenxiao_leve_model = new FenxiaoLevelModel();
        $fenxiao_level = $fenxiao_leve_model->getLevelList([ [ 'site_id', '=', $this->site_id ], [ 'status', '=', 1 ] ], '*', 'level_num asc,one_rate asc');
        $goods_info = $goods_model->getGoodsDetail($goods_id);
        if (empty($goods_info[ 'data' ]) || $goods_info[ 'data' ][ 'site_id' ] != $this->site_id) return $this->error('商品信息不存在');
        $fenxiao_config_model = new FenxiaoConfigModel();
        $fenxiao_config = $fenxiao_config_model->getFenxiaoBasicsConfig($this->site_id)['data'] ?? [];
        $fenxiao_config = $fenxiao_config['value'] ?? [];
        if (request()->isAjax()) {
            Db::startTrans();
            try {
                $fenxiao_type = input('fenxiao_type', 1);
                $fenxiao_skus = input('fenxiao', []);
                $is_fenxiao = input('is_fenxiao', 0);
                $fenxiao_price = input('fenxiao_price', []);
                $goods_data = [ 'is_fenxiao' => $is_fenxiao, 'fenxiao_type' => $fenxiao_type ];
                if ($fenxiao_type == 2) {
                    $fenxiao_goods_sku_data = [];
                    foreach ($fenxiao_skus as $level_id => $level_data) {
                        foreach ($level_data[ 'sku_id' ] as $key => $sku_id) {
                            $fenxiao_total = 0;
                            $fenxiao_level = [ 'one', 'two', 'three' ];
                            foreach ($fenxiao_level as $level) {
                                $item_rate_array = $level_data[ $level . '_rate' ] ?? [];
                                $item_rate = $item_rate_array[$key] ?? 0;
                                $item_money_array = $level_data[ $level . '_money' ] ?? [];
                                $item_money = $item_money_array[$key] ?? 0;
                                $var_rate_name = $level.'_rate';
                                $$var_rate_name = $item_rate;
                                $var_money_name = $level.'_money';
                                $$var_money_name = $item_money;
                                if ($item_rate > 0) {
                                    $fenxiao_total += $level_data[ 'sku_price' ][ $key ] * $item_rate / 100;
                                } elseif ($item_money > 0) {
                                    $fenxiao_total += $item_money;
                                }
                            }
                            if (empty($fenxiao_total)) {
                                return error(-1, '分销金额不可以为零');
                            }
                            if ($level_data[ 'sku_price' ][ $key ] < $fenxiao_total) {
                                return error(-1, '分销总金额不能大于商品sku价格的100%！');
                            }
                            if($fenxiao_config['level'] < 3){
                                $three_rate = 0;
                                $three_money = 0;
                                //通过分销设置的等级配置
                                if($fenxiao_config['level'] < 2){
                                    $two_rate = 0;
                                    $two_money = 0;
                                }
                            }
                            $fenxiao_sku = [
                                'goods_id' => $goods_id,
                                'level_id' => $level_id,
                                'sku_id' => $sku_id,
                                'one_rate' => $one_rate ?? 0,
                                'one_money' => $one_money ?? 0,
                                'two_rate' => $two_rate ?? 0,
                                'two_money' => $two_money ?? 0,
                                'three_rate' => $three_rate ?? 0,
                                'three_money' => $three_money ?? 0,
                            ];
                            $fenxiao_goods_sku_data[] = $fenxiao_sku;
                        }
                    }
                    $fenxiao_sku_model->deleteSku([ 'goods_id' => $goods_id ]);
                    $fenxiao_sku_model->addSkuList($fenxiao_goods_sku_data);
                }
                if ($fenxiao_type == 1) {
                    $fenxiao_goods_sku_data = [];
                    foreach ($fenxiao_level[ 'data' ] as $level) {
                        foreach ($goods_info[ 'data' ][ 'sku_data' ] as $sku) {
                            $item_one_rate = $level[ 'one_rate' ] ?? 0;
                            $item_two_rate = $level[ 'two_rate' ] ?? 0;
                            $item_three_rate = $level[ 'three_rate' ] ?? 0;
                            //通过分销设置的等级配置
                            if($fenxiao_config['level'] < 3){
                                $item_three_rate = 0;
                                if($fenxiao_config['level'] < 2){
                                    $item_two_rate = 0;
                                }
                            }
                            $fenxiao_sku = [
                                'goods_id' => $goods_id,
                                'level_id' => $level[ 'level_id' ],
                                'sku_id' => $sku[ 'sku_id' ],
                                'one_rate' => $item_one_rate,
                                'one_money' => 0,
                                'two_rate' => $item_two_rate,
                                'two_money' => 0,
                                'three_rate' => $item_three_rate,
                                'three_money' => 0,
                            ];
                            $fenxiao_goods_sku_data[] = $fenxiao_sku;
                        }
                    }
                    $fenxiao_sku_model->deleteSku([ 'goods_id' => $goods_id ]);
                    $fenxiao_sku_model->addSkuList($fenxiao_goods_sku_data);
                }
                $fenxiao_goods_model = new FenxiaoGoodsModel();
                $re = $fenxiao_goods_model->editGoodsFenxiao($goods_data, [ [ 'goods_id', '=', $goods_id ], [ 'site_id', '=', $this->site_id ] ]);
                if ($is_fenxiao) {
                    foreach ($fenxiao_price as $sku_id => $fenxiao_price) {
                        if (empty($fenxiao_price) || $fenxiao_price < 0) $fenxiao_price = 0;
                        $res = model('goods_sku')->update([ 'fenxiao_price' => $fenxiao_price ], [ [ 'sku_id', '=', $sku_id ], [ 'site_id', '=', $this->site_id ] ]);
                    }
                }
                Db::commit();
                return $re;
            } catch (Exception $e) {
                Db::rollback();
                return error(-1, $e->getMessage());
            }
        }
        $fenxiao_skus = $fenxiao_sku_model->getSkuList([ 'goods_id' => $goods_id ]);
        $skus = [];
        foreach ($fenxiao_skus[ 'data' ] as $fenxiao_sku) {
            $skus[ $fenxiao_sku[ 'level_id' ] . '_' . $fenxiao_sku[ 'sku_id' ] ] = $fenxiao_sku;
        }
        $goods_info[ 'data' ][ 'fenxiao_skus' ] = $skus;
        $goods_info[ 'data' ][ 'goods_image' ] = explode(',', $goods_info[ 'data' ][ 'goods_image' ]);
        $this->assign('fenxiao_level', $fenxiao_level[ 'data' ]);
        $this->assign('goods_info', $goods_info[ 'data' ]);
        $this->assign('fenxiao_config', $fenxiao_config);
        return $this->fetch("goods/config");
    }
    /**
     * 修改分销状态
     */
    public function modify()
    {
        if (request()->isAjax()) {
            $fenxiao_goods_model = new FenxiaoGoodsModel();
            $goods_id = input('goods_id');
            $is_fenxiao = input('is_fenxiao', 0);
            return $fenxiao_goods_model->modifyGoodsFenxiaoStatus($goods_id, $is_fenxiao ? 0 : 1, $this->site_id);
        }
    }
    /**
     * 批量设置是否参与分销
     * @return array
     */
    public function setGoodsIsFenxiao()
    {
        if (request()->isAjax()) {
            $fenxiao_goods_model = new FenxiaoGoodsModel();
            $goods_ids = input('goods_ids', '');
            $is_fenxiao = input('is_fenxiao', 0);
            return $fenxiao_goods_model->modifyGoodsIsFenxiao($goods_ids, $is_fenxiao, $this->site_id);
        }
    }
}