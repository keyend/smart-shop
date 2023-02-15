<?php
namespace app\model\goods;
use think\facade\Cache;
use app\model\BaseModel;
use app\model\system\Stat;
/**
 * 商品浏览历史
 */
class GoodsBrowse extends BaseModel
{
    /**
     * 添加商品浏览记录
     * @param array $data
     */
    public function addBrowse($data)
    {
        $res                 = model('goods_browse')->getInfo([['member_id', '=', $data['member_id']], ['goods_id', '=', $data['goods_id']]], 'site_id,id');
        $data['browse_time'] = time();
        if (!empty($res)) {
            $collect_id = model('goods_browse')->update($data, [['id', '=', $res['id']]]);
        } else {
            $collect_id = model('goods_browse')->add($data);
        }
        //添加浏览统计
        $stat = new Stat();
        $stat->addShopStat(['visit_count' => 1, 'site_id' => $data['site_id']]);
        return $this->success($collect_id);
    }
    /**
     * 删除浏览记录
     * @param int $id
     * @param int $member_id
     */
    public function deleteBrowse($id, $member_id)
    {
        $res = model('goods_browse')->delete([['member_id', '=', $member_id], ['id', '=', $id]]);
        return $this->success($res);
    }
    /**
     * 获取浏览记录分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getBrowsePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'browse_time desc', $field = 'id,member_id,browse_time,goods_id,sku_id', $alias = 'a', $join = [])
    {
        $list = model('goods_browse')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
        return $this->success($list);
    }
}