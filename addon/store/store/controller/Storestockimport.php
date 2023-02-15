<?php
namespace addon\store\store\controller;
use addon\store\model\StoreGoodsSku;
use addon\store\model\StoreStockImport as StoreStockImportModel;
class Storestockimport extends BaseStore
{
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
    }
    /**
     * 批量发货（订单导入）
     * @return array|mixed
     */
    public function lists()
    {
        if(request()->isAjax()){
            $page_index = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $model = new StoreStockImportModel();
            $list = $model->getStoreStockImportPageList([['store_id','=',$this->store_id],['site_id','=',$this->site_id]],$page_index,$page_size);
            return $list;
        }
        return $this->fetch('storestockimport/lists', [], $this->replace);
    }
    /**
     * 导出门店商品
     */
    public function exportStoreGoods()
    {
        $goods_ids = input('goods_ids', '');
        $store_goods_sku_model = new StoreGoodsSku();
        $list_result = $store_goods_sku_model->getGoodsSkuList([['gs.goods_id', 'in', $goods_ids], ['gs.site_id', '=', $this->site_id]], $this->store_id);
        $list = $list_result['data'];
        // 实例化excel
        $phpExcel = new \PHPExcel();
        $phpExcel->getProperties()->setTitle("门店库存导入模板");
        $phpExcel->getProperties()->setSubject("门店库存导入模板");
        //单独添加列名称
        $phpExcel->setActiveSheetIndex(0);
        $phpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $phpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
        $phpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $phpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
        $phpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $phpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $phpExcel->getActiveSheet()->setCellValue('A1', '商品编号');
        $phpExcel->getActiveSheet()->setCellValue('B1', '商品名称');
        $phpExcel->getActiveSheet()->setCellValue('C1', '商品sku编码');
        $phpExcel->getActiveSheet()->setCellValue('D1', 'sku名称');
        $phpExcel->getActiveSheet()->setCellValue('E1', '当前库存');
        $phpExcel->getActiveSheet()->setCellValue('F1', '库存（增/减）');
        foreach($list as $k=>$v){
            $start = $k + 2;
            $phpExcel->getActiveSheet()->setCellValue('A'. $start, $v['goods_id']);
            $phpExcel->getActiveSheet()->setCellValue('B'. $start, $v['goods_name']."\t");
            $phpExcel->getActiveSheet()->setCellValue('C'. $start, $v['sku_id']);
            $phpExcel->getActiveSheet()->setCellValue('D'. $start, $v['sku_name']."\t");
            $store_stock = $v['store_stock'] ? $v['store_stock'] : 0;
            $phpExcel->getActiveSheet()->setCellValue('E'. $start, $store_stock);
            $phpExcel->getActiveSheet()->setCellValue('F'. $start, '');
        }
        // 重命名工作sheet
        $phpExcel->getActiveSheet()->setTitle('门店库存导入模板');
        // 设置第一个sheet为工作的sheet
        $phpExcel->setActiveSheetIndex(0);
        // 保存Excel 2007格式文件，保存路径为当前路径，名字为export.xlsx
        $objWriter = \PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
        $file = date('Y年m月d日-门店库存导入模板', time()) . '.xlsx';
        $objWriter->save($file);
        header("Content-type:application/octet-stream");
        $filename = basename($file);
        header("Content-Disposition:attachment;filename = " . $filename);
        header("Accept-ranges:bytes");
        header("Accept-length:" . filesize($file));
        readfile($file);
        unlink($file);
        exit;
    }
    /**
     * 导入
     */
    public function import()
    {
        if (request()->isAjax()) {
            $filename = input('filename', '');
            $path = input('path', '');
            $store_sku__model = new StoreGoodsSku();
            $res = $store_sku__model->importStoreGoodsStock(['filename' => $filename, 'path' => $path], $this->site_id,$this->store_id);
            return $res;
        }
    }
    /**
     * 删除导入的订单文件记录
     */
    public function delete()
    {
        if(request()->isAjax()){
            $id = input('id','');
            $model = new StoreStockImportModel();
            $res = $model -> deleteStoreStockImport($id,$this->store_id);
            return $res;
        }
    }
    /**
     * 记录
     * @return array|mixed
     */
    public function detail()
    {
        $model = new StoreStockImportModel();
        $file_id = input('file_id',0);
        if(request()->isAjax()){
            $page_index = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $condition = [
                ['file_id','=',$file_id],
                ['store_id','=',$this->store_id]
            ];
            $status = input('status','');
            if($status !== ''){
                $condition[] = ['status','=',$status];
            }
            $list = $model->getStoreStockImportPageLogList($condition,$page_index,$page_size);
            return $list;
        }
        $this->assign('file_id',$file_id);
        $info = $model->getStoreStockImportInfo([['id','=',$file_id],['store_id','=',$this->store_id]]);
        $this->assign('info',$info['data']);
        return $this->fetch('storestockimport/detail', [], $this->replace);
    }
}