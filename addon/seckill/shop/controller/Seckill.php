<?php
namespace addon\seckill\shop\controller;
use app\shop\controller\BaseShop;
use addon\seckill\model\Seckill as SeckillModel;
/**
 * 秒杀控制器
 */
class Seckill extends BaseShop
{
    /**
     * 秒杀时间段列表
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $condition[] = ['site_id', '=', $this->site_id];
            $order       = 'seckill_start_time asc';
            $field       = '*';
            $seckill_model = new SeckillModel();
            $res           = $seckill_model->getSeckillList($condition, $field, $order, null);
            foreach ($res['data'] as $key => $val) {
                $val                                          = $seckill_model->transformSeckillTime($val);
                $res['data'][$key]['seckill_start_time_show'] = "{$val['start_hour']}:{$val['start_minute']}:{$val['start_second']}";
                $res['data'][$key]['seckill_end_time_show']   = "{$val['end_hour']}:{$val['end_minute']}:{$val['end_second']}";
            }
            return $res;
        } else {
            $this->forthMenu();
            return $this->fetch("seckill/lists");
        }
    }
    /**
     * 添加秒杀时间段
     */
    public function add()
    {
        if (request()->isAjax()) {
            $start_hour   = input('start_hour', 0);
            $start_minute = input('start_minute', 0);
            $start_second = input('start_second', 0);
            $end_hour   = input('end_hour', 0);
            $end_minute = input('end_minute', 0);
            $end_second = input('end_second', 0);
            $data          = [
                'site_id'            => $this->site_id,
                'name'               => input('name', ''),
                'seckill_start_time' => $start_hour * 3600 + $start_minute * 60 + $start_second,
                'seckill_end_time'   => $end_hour * 3600 + $end_minute * 60 + $end_second,
                'create_time'        => time(),
            ];
            $seckill_model = new SeckillModel();
            return $seckill_model->addSeckill($data);
        } else {
            return $this->fetch("seckill/add");
        }
    }
    /**
     * 编辑秒杀时间段
     */
    public function edit()
    {
        $seckill_model = new SeckillModel();
        if (request()->isAjax()) {
            $start_hour   = input('start_hour', 0);
            $start_minute = input('start_minute', 0);
            $start_second = input('start_second', 0);
            $end_hour   = input('end_hour', 0);
            $end_minute = input('end_minute', 0);
            $end_second = input('end_second', 0);
            $data = [
                'name'               => input('name', ''),
                'seckill_start_time' => $start_hour * 3600 + $start_minute * 60 + $start_second,
                'seckill_end_time'   => $end_hour * 3600 + $end_minute * 60 + $end_second,
                'create_time'        => time(),
                'seckill_id'         => input('seckill_id', 0),
            ];
            return $seckill_model->editSeckill($data, $this->site_id);
        } else {
            $seckill_id = input('seckill_id', 0);
            $this->assign('seckill_id', $seckill_id);
            //秒杀详情
            $seckill_info = $seckill_model->getSeckillInfo([['seckill_id', '=', $seckill_id]]);
            if (!empty($seckill_info['data'])) {
                $seckill_info['data'] = $seckill_model->transformSeckillTime($seckill_info['data']);
            }
            $this->assign('seckill_info', $seckill_info['data']);
            return $this->fetch("seckill/edit");
        }
    }
    /**
     * 删除秒杀时间段
     */
    public function delete()
    {
        if (request()->isAjax()) {
            $seckill_id    = input('seckill_id', 0);
            $seckill_model = new SeckillModel();
            return $seckill_model->deleteSeckill($seckill_id);
        }
    }
    /**
     * 秒杀商品
     */
    public function goods()
    {
        $seckill_model = new SeckillModel();
        if (request()->isAjax()) {
            $page       = input('page', 1);
            $page_size  = input('page_size', PAGE_LIST_ROWS);
            $seckill_id = input('seckill_id', 0);
            $sku_name   = input('sku_name', '');
            $condition   = [];
            $condition[] = ['ngs.site_id', '=', $this->site_id];
            $condition[] = ['ngs.sku_name', 'like', '%' . $sku_name . '%'];
            $condition[] = ['npsg.seckill_id', '=', $seckill_id];
            $res = $seckill_model->getSeckillGoodsPageList($condition, $page, $page_size);
            foreach ($res['data']['list'] as $key => $val) {
                if ($val['price'] != 0) {
                    $discount_rate = floor($val['seckill_price'] / $val['price'] * 100);
                } else {
                    $discount_rate = 100;
                }
                $res['data']['list'][$key]['discount_rate'] = $discount_rate;
            }
            return $res;
        } else {
            $seckill_id = input('seckill_id', 0);
            $this->assign('seckill_id', $seckill_id);
            //秒杀详情
            $seckill_info = $seckill_model->getSeckillInfo([['seckill_id', '=', $seckill_id]]);
            if (!empty($seckill_info['data'])) {
                $seckill_info['data'] = $seckill_model->transformSeckillTime($seckill_info['data']);
            }
            $this->assign('seckill_info', $seckill_info['data']);
            return $this->fetch("seckill/goods");
        }
    }
    /**
     * 添加商品
     */
    public function addGoods()
    {
        if (request()->isAjax()) {
            $seckill_id = input('seckill_id', 0);
            $sku_ids    = input('sku_ids', '');
            $site_id    = $this->site_id;
            $seckill_model = new SeckillModel();
            return $seckill_model->addSeckillGoods($seckill_id, $site_id, $sku_ids);
        }
    }
    /**
     * 更新商品（秒杀价格）
     */
    public function updateGoods()
    {
        if (request()->isAjax()) {
            $seckill_id = input('seckill_id', 0);
            $sku_id     = input('sku_id', '');
            $site_id    = $this->site_id;
            $price      = input('price', 0.00);
            $seckill_model = new SeckillModel();
            return $seckill_model->editSeckillGoods($seckill_id, $site_id, $sku_id, $price);
        }
    }
    /**
     * 更新限购数量
     */
    public function updateGoodsNum()
    {
        if (request()->isAjax()) {
            $seckill_id = input('seckill_id', 0);
            $sku_id     = input('sku_id', '');
            $site_id    = $this->site_id;
            $num     = input('num', 1);
            $seckill_model = new SeckillModel();
            return $seckill_model->editSeckillGoodsNum($seckill_id, $site_id, $sku_id, $num);
        }
    }
    /**
     * 删除商品
     */
    public function deleteGoods()
    {
        if (request()->isAjax()) {
            $seckill_id = input('seckill_id', 0);
            $sku_id     = input('sku_id', '');
            $site_id    = $this->site_id;
            $seckill_model = new SeckillModel();
            return $seckill_model->deleteSeckillGoods($seckill_id, $site_id, $sku_id);
        }
    }
    /**
     * 秒杀商品
     */
    public function goodslist()
    {
        if (request()->isAjax()) {
            $page      = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $sku_name  = input('sku_name', '');
            $condition   = [];
            $condition[] = ['ngs.site_id', '=', $this->site_id];
            $condition[] = ['ngs.sku_name', 'like', '%' . $sku_name . '%'];
            $seckill_model = new SeckillModel();
            $res           = $seckill_model->getSeckillGoodsPageList($condition, $page, $page_size);
            foreach ($res['data']['list'] as $key => $val) {
                if ($val['price'] != 0) {
                    $discount_rate = floor($val['seckill_price'] / $val['price'] * 100);
                } else {
                    $discount_rate = 100;
                }
                $res['data']['list'][$key]['discount_rate']           = $discount_rate;
                $val                                                  = $seckill_model->transformSeckillTime($val);
                $res['data']['list'][$key]['seckill_start_time_show'] = "{$val['start_hour']}:{$val['start_minute']}:{$val['start_second']}";
                $res['data']['list'][$key]['seckill_end_time_show']   = "{$val['end_hour']}:{$val['end_minute']}:{$val['end_second']}";
            }
            return $res;
        } else {
            $this->forthMenu();
            return $this->fetch("seckill/goodslist");
        }
    }
}