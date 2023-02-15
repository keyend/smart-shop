<?php
namespace addon\printer\shop\controller;
use addon\printer\model\PrinterOrder;
use app\model\store\Store;
use app\shop\controller\BaseShop;
use addon\printer\model\PrinterTemplate;
use addon\printer\model\Printer as PrinterModel;
use app\model\order\OrderCommon as OrderCommonModel;
class Printer extends BaseShop
{
    /*
     *  小票打印列表
     */
    public function lists()
    {
        $model = new PrinterModel();
        if (request()->isAjax()) {
            $condition[] = ['site_id', '=', $this->site_id];
            $page        = input('page', 1);
            $page_size   = input('page_size', PAGE_LIST_ROWS);
            $list        = $model->getPrinterPageList($condition, $page, $page_size, 'printer_id desc');
            return $list;
        }
        return $this->fetch("printer/lists");
    }
    /**
     * 添加小票打印
     */
    public function add()
    {
        $model = new PrinterModel();
        if (request()->isAjax()) {
            $data               = [
                'site_id'       => $this->site_id,
                'template_name' => input('template_name', ''),
                'brand'         => input('brand', ''),
                'printer_name' => input('printer_name', ''),
                'printer_code' => input('printer_code', ''),
                'printer_key'  => input('printer_key', ''),
                'open_id'      => input('open_id', ''),
                'apikey'       => input('apikey', ''),
                'print_num'    => input('print_num', ''),
                'order_type'   => input('order_type', ''),
                'template_id'  => input('template_id', ''),
                'store_id' => input('store_id','')
            ];
            $data['order_type'] = ',' . $data['order_type'] . ',';
            return $model->addPrinter($data);
        } else {
            //模板列表
            $template_model = new PrinterTemplate();
            $condition      = [
                ['site_id', '=', $this->site_id],
            ];
            $template_list  = $template_model->getPrinterTemplateList($condition, 'template_id,template_name', 'template_id desc');
            $this->assign('template_list', $template_list['data']);
            //打印机品牌
            $brand = $model->getPrinterBrand();
            $this->assign('brand', $brand);
            //订单类型
            $order_common_model = new OrderCommonModel();
            $order_type_list    = $order_common_model->getOrderTypeStatusList();
            unset($order_type_list['all']);
            $this->assign("order_type_list", $order_type_list);
            $is_exit_store = addon_is_exit('store');
            if($is_exit_store == 1){
                $store_model = new Store();
                $store_field = 'store_id,store_name';
                $store_list  = $store_model->getStoreList([['site_id', '=', $this->site_id]],$store_field);
                $this->assign('store_list',$store_list['data']);
            }
            $this->assign('is_exit_store',$is_exit_store);
            return $this->fetch("printer/add");
        }
    }
    /**
     * 编辑小票打印
     */
    public function edit()
    {
        $model      = new PrinterModel();
        $printer_id = input('printer_id', 0);
        if (request()->isAjax()) {
            $data               = [
                'printer_id'    => $printer_id,
                'site_id'       => $this->site_id,
                'template_name' => input('template_name', ''),
                'printer_name'  => input('printer_name', ''),
                'print_num'     => input('print_num', ''),
                'order_type'    => input('order_type', ''),
                'template_id'   => input('template_id', ''),
                'store_id' => input('store_id','')
            ];
            $data['order_type'] = ',' . $data['order_type'] . ',';
            return $model->editPrinter($data);
        } else {
            $info                       = $model->getPrinterInfo([['printer_id', '=', $printer_id], ['site_id', '=', $this->site_id]]);
            $info['data']['order_type'] = explode(',', $info['data']['order_type']);
            $this->assign('printer_info', $info['data']);
            //模板列表
            $template_model = new PrinterTemplate();
            $condition      = [
                ['site_id', '=', $this->site_id],
            ];
            $template_list  = $template_model->getPrinterTemplateList($condition, 'template_id,template_name', 'template_id desc');
            $this->assign('template_list', $template_list['data']);
            //打印机品牌
            $brand = $model->getPrinterBrand();
            $this->assign('brand', $brand);
            //订单类型
            $order_common_model = new OrderCommonModel();
            $order_type_list    = $order_common_model->getOrderTypeStatusList();
            unset($order_type_list['all']);
            $this->assign("order_type_list", $order_type_list);
            //是否存在门店
            $is_exit_store = addon_is_exit('store');
            if($is_exit_store == 1){
                $store_model = new Store();
                $store_field = 'store_id,store_name';
                $store_list  = $store_model->getStoreList([['site_id', '=', $this->site_id]],$store_field);
                $this->assign('store_list',$store_list['data']);
            }
            $this->assign('is_exit_store',$is_exit_store);
            return $this->fetch("printer/edit");
        }
    }
    /*
     *  删除
     */
    public function delete()
    {
        $printer_id = input('printer_id', '');
        $printer_model = new PrinterModel();
        return $printer_model->deletePrinter([['printer_id', '=', $printer_id], ['site_id', '=', $this->site_id]]);
    }
    /**
     * 测试打印
     */
    public function testPrint()
    {
        $printer_id  = input('printer_id', '');
        $print_model = new PrinterOrder();
        $res         = $print_model->testPrint($printer_id, $this->site_id);
        dump($res);
    }
}