<?php
namespace addon\live\model;
use app\model\BaseModel;
use app\model\system\Cron;
use app\model\upload\Upload;
use think\facade\Log;
class Goods extends BaseModel
{
    private $liveStatus = [
        0 => '未审核',
        1 => '审核中',
        2 => '审核通过',
        3 => '审核驳回'
    ];
    /**
     * 获取直播间列表
     * @param array $condition
     * @param bool $field
     * @param string $order
     * @param int $page
     * @param int $list_rows
     * @param string $alias
     * @param array $join
     * @return array
     */
    public function getGoodsPageList($condition = [], $field = true, $order = '', $page = 1, $list_rows = PAGE_LIST_ROWS, $alias = 'a', $join = [])
    {
        $data = model('weapp_goods')->pageList($condition, $field, $order, $page, $list_rows, $alias, $join);
        if (!empty($data['list'])) {
            foreach ($data['list'] as $k => $item) {
                $data['list'][$k]['status_name'] = $this->liveStatus[$item['status']] ?? '';
            }
        }
        return $this->success($data);
    }
    /**
     * 同步商品库商品
     */
    public function syncGoods($start, $limit, $site_id, $status = 2)
    {
        $live   = new Live($site_id);
        $result = $live->getGoodsList($start, $limit, $status);
        if ($result['code'] < 0) return $result;
        if (!empty($result['data']['goods'])) {
            $upload = new Upload($site_id, 'shop');
            $upload->setPath('upload/live/goods/');
            foreach ($result['data']['goods'] as $item) {
                preg_match("/(pages\/goods\/detail\/detail\?sku_id=)(\d*)$/", $item['url'], $matches);
                $data      = [
                    'site_id'         => $site_id,
                    'goods_id'        => $item['goodsId'],
                    'name'            => $item['name'],
                    'price'           => $item['price'],
                    'status'          => $status,
                    'url'             => $item['url'],
                    'sku_id'          => $matches[2] ?? 0,
                    'third_party_tag' => $item['thirdPartyTag']
                ];
                $room_info = model('weapp_goods')->getInfo([['goods_id', '=', $item['goodsId']], ['site_id', '=', $site_id]], 'id');
                if (empty($room_info)) {
                    if (is_url($item['coverImgUrl'])) {
                        $pull_result = $upload->remotePull($item['coverImgUrl']);
                        $pull_result = $pull_result['data'];
                        if (isset($pull_result['pic_path']) && !empty($pull_result['pic_path'])) {
                            $data['cover_img'] = $pull_result['pic_path'];
                        } else {
                            $data['cover_img'] = $item['coverImgUrl'];
                        }
                    }
                    model('weapp_goods')->add($data);
                } else {
                    model('weapp_goods')->update($data, [['id', '=', $room_info['id']]]);
                }
            }
            $total_page = ceil($result['data']['total'] / $limit);
            return $this->success(['page' => $start, 'total_page' => $total_page]);
        } else {
            return $this->success(['page' => $start, 'total_page' => 1]);
        }
    }
    /**
     * 添加商品
     * @param $param
     */
    public function addGoods($param)
    {
        if (!preg_match("/(pages\/goods\/detail\/detail\?sku_id=)(\d*)$/", $param['url'], $matches)) {
            return $this->error('', '商品链接格式不正确');
        }
        $live   = new Live($param['site_id']);
        if (is_url($param['goods_pic'])) {
            $upload = new Upload($param['site_id']);
            $goods_pic = $upload->setPath("common/temp/" . date("Ymd") . '/')->remotePullToLocal($param['goods_pic'])['data']['path'] ?? '';
            $result = $live->addImageMedia($goods_pic);
        } else {
            $result = $live->addImageMedia($param['goods_pic']);
        }
        if ($result['code'] < 0) return $result;
        $audit = [
            'goodsInfo' => [
                'coverImgUrl' => $result['data']['media_id'],
                'name'        => $param['name'],
                'priceType'   => $param['price_type'],
                'price'       => $param['price'],
                'price2'      => $param['price2'],
                'url'         => $param['url']
            ]
        ];
        if ($param['price_type'] == 1) unset($audit['goodsInfo']['price2']);
        $result = $live->addGoodsAudit($audit);
        if ($result['code'] < 0) return $result;
        $data   = [
            'site_id'         => $param['site_id'],
            'goods_id'        => $result['data']['goodsId'],
            'name'            => $param['name'],
            'cover_img'       => $param['goods_pic'],
            'price'           => $param['price'],
            'status'          => 1,
            'url'             => $param['url'],
            'audit_id'        => $result['data']['auditId'],
            'sku_id'          => $matches[2],
            'third_party_tag' => 2
        ];
        $result = model('weapp_goods')->add($data);
        $cron_info = model("cron")->getInfo([['event', '=', 'LiveGoodsStatus'], ['relate_id', '=', $param['site_id']]]);
        if (empty($cron_info)) {
            $cron = new Cron();
            $cron->addCron(2, 10, '小程序商品获取审核状态', 'LiveGoodsStatus', time(), $param['site_id']);
        }
        return $this->success($result);
    }
    /**
     * 删除商品
     * @param $id
     * @param $site_id
     */
    public function deleteGoods($id, $site_id)
    {
        $info = model('weapp_goods')->getInfo([['site_id', '=', $site_id], ['id', '=', $id]], 'goods_id');
        if (empty($info)) return $this->error('', '未获取到商品信息');
        $live   = new Live($site_id);
        $result = $live->deleteGoods($info['goods_id']);
        if ($result['code'] < 0) return $result;
        $res = model('weapp_goods')->delete([['site_id', '=', $site_id], ['id', '=', $id]]);
        return $this->success($res);
    }
    /**
     * 获取直播商品审核状态
     * @param $id
     */
    public function getGoodsAuditStatus($site_id)
    {
        $prefix = config("database")["connections"]["mysql"]["prefix"];
        $data   = model('weapp_goods')->query("SELECT GROUP_CONCAT(goods_id) as goods_id FROM {$prefix}weapp_goods WHERE site_id = {$site_id} AND status = 1");
        if (isset($data[0]) && isset($data[0]['goods_id']) && !empty($data[0]['goods_id'])) {
            $live   = new Live($site_id);
            $result = $live->getGoodsStatus(explode(',', $data[0]['goods_id']));
            if ($result['code'] < 0) return $result;
            foreach ($result['data'] as $item) {
                if ($item['audit_status'] != 1) {
                    model('weapp_goods')->update(['status' => $item['audit_status']], [['site_id', '=', $site_id], ['goods_id', '=', $item['goods_id']]]);
                }
            }
        }
    }
}