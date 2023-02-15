<?php
namespace addon\fenxiao\api\controller;
use addon\fenxiao\model\FenxiaoGoodsSku;
use addon\fenxiao\model\FenxiaoLevel;
use app\api\controller\BaseApi;
use addon\fenxiao\model\Fenxiao as FenxiaoModel;
use addon\fenxiao\model\FenxiaoGoodsCollect as FenxiaoGoodsCollectModel;
use addon\fenxiao\model\Config as ConfigModel;

/**
 * 分销商关注商品
 */
class Goodscollect extends BaseApi
{

    /**
     * 添加分销商关注商品
     * @return false|string
     */
    public function add()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        $goods_id = isset($this->params['goods_id']) ? $this->params['goods_id'] : 0;
        $sku_id   = isset($this->params['sku_id']) ? $this->params['sku_id'] : 0;

        if (empty($goods_id)) {
            return $this->response($this->error('', 'REQUEST_GOODS_ID'));
        }

        $fenxiao_model = new FenxiaoModel();
        $fenxiao_info  = $fenxiao_model->getFenxiaoInfo([['member_id', '=', $this->member_id]], "fenxiao_id");
        $fenxiao_info  = $fenxiao_info['data'];

        $data                    = [
            'member_id'  => $this->member_id,
            'fenxiao_id' => $fenxiao_info['fenxiao_id'],
            'goods_id'   => $goods_id,
            'sku_id'     => $sku_id,
            'site_id'    => $this->site_id
        ];
        $fenxiao_goods_sku_model = new FenxiaoGoodsCollectModel();
        $res                     = $fenxiao_goods_sku_model->addCollect($data);
        return $this->response($res);
    }

    /**
     * 删除分销商关注商品
     * @return false|string
     */
    public function delete()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        $collect_id = isset($this->params['collect_id']) ? $this->params['collect_id'] : 0;

        if (empty($collect_id)) {
            return $this->response($this->error('', 'REQUEST_COLLECT_ID'));
        }

        $fenxiao_model           = new FenxiaoModel();
        $fenxiao_info            = $fenxiao_model->getFenxiaoInfo([['member_id', '=', $this->member_id]], "fenxiao_id");
        $fenxiao_info            = $fenxiao_info['data'];
        $condition               = [
            ['fenxiao_id', '=', $fenxiao_info['fenxiao_id']],
            ['collect_id', '=', $collect_id]
        ];
        $fenxiao_goods_sku_model = new FenxiaoGoodsCollectModel();
        $res                     = $fenxiao_goods_sku_model->deleteCollect($condition);
        return $this->response($res);
    }

    /**
     * 分销商关注商品分页列表
     */
    public function page()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        $page      = isset($this->params['page']) ? $this->params['page'] : 1;
        $page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;

        // 获取当前用户的分销等级
        $fenxiao_model = new FenxiaoModel();
        $fenxiao_info  = $fenxiao_model->getFenxiaoInfo([['member_id', '=', $this->member_id]], "fenxiao_id,level_id");
        $fenxiao_info  = $fenxiao_info['data'];

        $fenxiao_level = new FenxiaoLevel();
        $level_info = $fenxiao_level->getLevelInfo([ ['level_id', '=', $fenxiao_info['level_id'] ] ], 'one_rate');
        $level_info = $level_info['data'];

        $condition = [
            ['g.is_fenxiao', '=', 1],
            ['gs.goods_state', '=', 1],
            ['gs.is_delete', '=', 0],
            ['fgc.member_id', '=', $this->member_id]
        ];

        $fenxiao_goods_collect_model = new FenxiaoGoodsCollectModel();
        $list = $fenxiao_goods_collect_model->getCollectPageList($condition, $page, $page_size);
        $fenxiao_goods_sku_model = new FenxiaoGoodsSku();

        // 计算佣金比率
        foreach ($list['data']['list'] as $k => $v) {
            $discount_price = $v['fenxiao_price'] > 0 ? $v['fenxiao_price'] : $v['discount_price'];

            $money = 0;
            if ($v['fenxiao_type'] == 1) {
                // 默认规则
                $money = number_format($discount_price * $level_info[ 'one_rate' ] / 100, 2, '.', '');
            } else {
                // 自定义规则
                $fenxiao_goods_sku_info = $fenxiao_goods_sku_model->getFenxiaoGoodsSkuInfo([ ['sku_id', '=', $v['sku_id'] ],['level_id', '=', $fenxiao_info['level_id'] ] ], 'one_money,one_rate');
                if (!empty($fenxiao_goods_sku_info['data'])) {
                    $fenxiao_goods_sku_info = $fenxiao_goods_sku_info['data'];
                    $money = $fenxiao_goods_sku_info[ 'one_money' ];
                    if ($fenxiao_goods_sku_info[ 'one_rate' ] > 0) {
                        $money = number_format($discount_price * $fenxiao_goods_sku_info[ 'one_rate' ] / 100, 2, '.', '');
                    }
                }
            }
            $list['data']['list'][$k]['commission_money'] = $money;
        }

        return $this->response($list);

    }

}