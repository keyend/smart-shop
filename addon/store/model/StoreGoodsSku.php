<?php
namespace addon\store\model;
use app\model\BaseModel;
use app\model\goods\Goods;
class StoreGoodsSku extends BaseModel
{
    /**
     * 添加店铺sku
     * @param unknown $pintuan_data
     * @param unknown $sku_list
     */
    public function addStoreGoodsSku($data)
    {
        $res = model('store_goods_sku')->add($data);
        //判断当前有没有store_goods
        $store_goods_info = model('store_goods')->getInfo([['goods_id', '=', $data['goods_id']], ['store_id', '=', $data['store_id']]], 'id');
        if (empty($store_goods_info)) {
            $store_goods_data = [
                'goods_id' => $data['goods_id'],
                'store_id' => $data['store_id'],
                'create_time' => time(),
            ];
            model('store_goods')->add($store_goods_data);
        }
        return $this->success($res);
    }
    /**
     * 获取门店商品sku详情
     * @param $condition
     * @param string $field
     * @return array
     */
    public function getStoreGoodsSkuInfo($condition, $field = '*')
    {
        $res = model('store_goods_sku')->getInfo($condition, $field);
        return $this->success($res);
    }
    /**
     * 增加库存
     * @param param
     */
    public function incStock($param)
    {
        $condition = array(
            ['store_id', '=', $param['store_id']],
            ['sku_id', '=', $param['sku_id']]
        );
        $num = $param['store_stock'];
        $store_sku_info = model("store_goods_sku")->getInfo($condition, 'id, goods_id');
        if (empty($store_sku_info))
            return $this->error(-1, "");
        //编辑sku库存
        $result = model("store_goods_sku")->setInc($condition, 'store_stock', $num);
        model('store_goods')->setInc([['goods_id', '=', $store_sku_info['goods_id']], ['store_id', '=', $param['store_id']]], 'store_goods_stock', $num);
        return $this->success($result);
    }
    /**
     * 减少库存
     * @param param
     */
    public function decStock($param)
    {
        $condition = array(
            ['store_id', '=', $param['store_id']],
            ['sku_id', '=', $param['sku_id']]
        );
        $num = $param['store_stock'];
        $store_sku_info = model("store_goods_sku")->getInfo($condition, 'id, goods_id, store_stock');
        if (empty($store_sku_info)) {
            return $this->error(-1, '库存不足！');
        }
        if (($store_sku_info['store_stock'] - $num) < 0) {
            return $this->error(-1, '库存不足！');
        }
        //编辑sku库存
        $result = model("store_goods_sku")->setDec($condition, 'store_stock', $num);
        model('store_goods')->setDec([['goods_id', '=', $store_sku_info['goods_id']], ['store_id', '=', $param['store_id']]], 'store_goods_stock', $num);
        return $this->success($result);
    }
    /**
     * 编辑门店商品库存信息
     * @param $goods_sku_array
     */
    public function editStock($store_goods_sku_array)
    {
        model('store_goods_sku')->startTrans();
        try {
            foreach ($store_goods_sku_array as $item) {
                $sku_info_result = $this->getStoreGoodsSkuInfo([["store_id", "=", $item["store_id"]], ["sku_id", "=", $item["sku_id"]]], "sku_id");
                $sku_info = $sku_info_result["data"];
                if (empty($sku_info)) {
                    $sku_data = array(
                        "goods_id" => $item["goods_id"],
                        "sku_id" => $item["sku_id"],
                        "store_id" => $item["store_id"],
                    );
                    $this->addStoreGoodsSku($sku_data);
                }
                if ($item["store_stock"] > 0) {
                    $item_result = $this->incStock($item);
                } else {
                    $item["store_stock"] = abs($item["store_stock"]);
                    $item_result = $this->decStock($item);
                }
                if ($item_result["code"] < 0) {
                    model('store_goods_sku')->rollback();
                    return $item_result;
                }
            }
            model('store_goods_sku')->commit();
            return $this->success();
        } catch (\Exception $e) {
            model('store_goods_sku')->rollback();
            return $this->error($e->getMessage());
        }
    }
    /**
     * 获取商品列表
     * @param array $condition
     */
    public function getGoodsSkuList($condition = [], $store_id = 0)
    {
        $alias = 'gs';
        $join = [
            [
                'store_goods_sku sgs',
                'sgs.sku_id = gs.sku_id and (sgs.store_id is null or sgs.store_id = ' . $store_id . ')',
                'left'
            ]
        ];
        $field = 'gs.*,sgs.store_stock,sgs.store_sale_num';
        $list = model('goods_sku')->getList($condition, $field, '', $alias, $join);
        return $this->success($list);
    }
    /**
     * 获取商品分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getGoodsSkuPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'gs.create_time desc', $field = '*')
    {
        $alias = 'gs';
        $join = [
            [
                'store_goods_sku sgs',
                'sgs.sku_id = gs.sku_id',
                'inner'
            ],
            [
                'goods g',
                'g.goods_id = gs.goods_id',
                'inner'
            ]
        ];
        $field = 'gs.*,sgs.store_stock,sgs.store_sale_num';
        $list = model('goods_sku')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
        return $this->success($list);
    }
    /**
     * 门店库存批量修改（导入excel）
     * @param $param
     * @param $site_id
     * @return array
     */
    public function importStoreGoodsStock($param, $site_id, $store_id)
    {
        $PHPExcel = new \PHPExcel();
        //如果excel文件后缀名为.xls，导入这个类
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        //载入文件
        $PHPExcel = $PHPReader->load($param['path']);
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
        $currentSheet = $PHPExcel->getSheet(0);
        //获取总行数
        $allRow = $currentSheet->getHighestRow();
        if ($allRow < 2) {
            return $this->error('', '导入了一个空文件');
        }
        //添加文件上传记录
        $success_num = $allRow - 1;
        $error_num = 0;
        $data = [
            'site_id' => $site_id,
            'store_id' => $store_id,
            'filename' => $param['filename'],
            'path' => $param['path'],
            'sku_num' => $allRow - 1,
            'success_num' => $success_num,
            'create_time' => time()
        ];
        $res = model('store_stock_import')->add($data);
        if (!$res) {
            return $this->error('', '上传文件失败');
        }
        model('store_stock_import')->startTrans();
        try {
            for ($i = 2; $i <= $allRow; $i++) {
                //商品id
                $goods_id = $PHPExcel->getActiveSheet()->getCell('A' . $i)->getValue();
                $goods_id = trim($goods_id, ' ');
                //商品名称
                $goods_name = $PHPExcel->getActiveSheet()->getCell('B' . $i)->getValue();
                $goods_name = trim($goods_name, ' ');
                //sku_id
                $sku_id = $PHPExcel->getActiveSheet()->getCell('C' . $i)->getValue();
                $sku_id = trim($sku_id, ' ');
                //sku名称
                $sku_name = $PHPExcel->getActiveSheet()->getCell('D' . $i)->getValue();
                $sku_name = trim($sku_name, ' ');
                //库存（增/减）
                $stock = $PHPExcel->getActiveSheet()->getCell('F' . $i)->getValue();
                $stock = trim($stock, ' ');
                if (empty($goods_id) || empty($sku_id) || empty($stock)) {
                    $error_num += 1;
                    $success_num -= 1;
                    model('store_stock_import')->update(['success_num' => $success_num, 'error_num' => $error_num], [['id', '=', $res]]);
                    //添加失败记录
                    model('store_stock_import_log')->add(
                        [
                            'store_id' => $store_id,'file_id' => $res, 'goods_id' => $goods_id, 'goods_name' => $goods_name, 'sku_id' => $sku_id,
                            'sku_name' => $sku_name, 'stock' => $stock, 'status' => -1,'reason' => '商品编号、sku编号或者库存为空'
                        ]
                    );
                    continue;
                }
                $goods_model = new Goods();
                $sku_info = $goods_model->getGoodsSkuInfo([['sku_id','=',$sku_id],['goods_id','=',$goods_id],['site_id','=',$site_id]]);
                if(empty($sku_info['data'])){
                    $error_num += 1;
                    $success_num -= 1;
                    model('store_stock_import')->update(['success_num' => $success_num, 'error_num' => $error_num], [['id', '=', $res]]);
                    //添加失败记录
                    model('store_stock_import_log')->add(
                        [
                            'store_id' => $store_id,'file_id' => $res, 'goods_id' => $goods_id, 'goods_name' => $goods_name, 'sku_id' => $sku_id,
                            'sku_name' => $sku_name, 'stock' => $stock, 'status' => -1,'reason' => '商品不存在'
                        ]
                    );
                    continue;
                }
                $sku_info_result = $this->getStoreGoodsSkuInfo([["store_id", "=", $store_id], ["sku_id", "=", $sku_id]], "sku_id");
                $sku_info = $sku_info_result["data"];
                if (empty($sku_info)) {
                    $sku_data = array(
                        "goods_id" => $goods_id,
                        "sku_id" => $sku_id,
                        "store_id" => $store_id,
                    );
                    $this->addStoreGoodsSku($sku_data);
                }
                $store_data = [
                    'store_id' => $store_id,
                    'sku_id' => $sku_id,
                    'store_stock' => $stock
                ];
                if ($stock > 0) {
                    $item_result = $this->incStock($store_data);
                } else {
                    $store_data["store_stock"] = abs($store_data["store_stock"]);
                    $item_result = $this->decStock($store_data);
                }
                if ($item_result["code"] < 0) {
                    $error_num += 1;
                    $success_num -= 1;
                    model('store_stock_import')->update(['success_num' => $success_num, 'error_num' => $error_num], [['id', '=', $res]]);
                    //添加失败记录
                    model('store_stock_import_log')->add(
                        [
                            'store_id' => $store_id,'file_id' => $res, 'goods_id' => $goods_id, 'goods_name' => $goods_name, 'sku_id' => $sku_id,
                            'sku_name' => $sku_name, 'stock' => $stock, 'status' => -1, 'reason' => $item_result['message']
                        ]
                    );
                    continue;
                }
                //添加记录
                model('store_stock_import_log')->add(
                    [
                        'store_id' => $store_id,'file_id' => $res, 'goods_id' => $goods_id, 'goods_name' => $goods_name, 'sku_id' => $sku_id,
                        'sku_name' => $sku_name, 'stock' => $stock, 'status' => 0, 'reason' => ''
                    ]
                );
            }
            model('store_stock_import')->commit();
            return $this->success();
        } catch (\Exception $e) {
            model('store_stock_import')->rollback();
            model('store_stock_import')->update(['success_num' => 0, 'error_num' => $allRow - 1], [['id', '=', $res]]);
            return $this->error('', $e->getMessage());
        }
    }
}