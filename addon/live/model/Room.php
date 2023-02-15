<?php
namespace addon\live\model;
use app\model\BaseModel;
use app\model\system\Cron;
use app\model\upload\Upload;
class Room extends BaseModel
{
    private $liveStatus = [
        '101' => '直播中',
        '102' => '未开始',
        '103' => '已结束',
        '104' => '禁播',
        '105' => '暂停中',
        '106' => '异常',
        '107' => '已过期',
    ];
    /**
     * 创建直播间
     * @param $data
     * @param $site_id
     */
    public function createRoom($data, $site_id)
    {
        $live = new Live($site_id);
        $res  = $live->createRoom($data);
        if ($res['code'] == 0) {
            $this->syncLiveRoom(0, 1, $site_id);
            $cron_info = model("cron")->getInfo([['event', '=', 'LiveRoomStatus'], ['relate_id', '=', $site_id]]);
            if (empty($cron_info)) {
                $cron = new Cron();
                $cron->addCron(2, 10, '轮询小程序直播状态', 'LiveRoomStatus', time(), $site_id);
            }
        }
        return $res;
    }
    /**
     * 编辑直播间信息
     * @param array $data
     * @param array $where
     */
    public function updateRoomInfo($data = [], $where = [])
    {
        $res = model('weapp_live_room')->update($data, $where);
        return $this->success($res);
    }
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
    public function getRoomPageList($condition = [], $field = true, $order = '', $page = 1, $list_rows = PAGE_LIST_ROWS, $alias = 'a', $join = [])
    {
        $data = model('weapp_live_room')->pageList($condition, $field, $order, $page, $list_rows, $alias, $join);
        if (!empty($data['list'])) {
            foreach ($data['list'] as $k => $item) {
                $data['list'][$k]['status_name'] = $this->liveStatus[$item['live_status']] ?? '';
                if (isset($item['goods'])) $data['list'][$k]['goods'] = json_decode($item['goods'], true);
            }
        }
        return $this->success($data);
    }
    /**
     * 同步直播间列表
     */
    public function syncLiveRoom($start, $limit, $site_id)
    {
        $live   = new Live($site_id);
        $result = $live->getRoomList($start, $limit);
        if ($result['code'] < 0) return $result;
        if (!empty($result['data']['list'])) {
            $upload = new Upload($site_id, 'shop');
            $upload->setPath('upload/live/room/');
            foreach ($result['data']['list'] as $item) {
                $data      = [
                    'site_id'     => $site_id,
                    'roomid'      => $item['roomid'],
                    'name'        => $item['name'],
                    'cover_img'   => $item['cover_img'],
                    'start_time'  => $item['start_time'],
                    'end_time'    => $item['end_time'],
                    'anchor_name' => $item['anchor_name'],
                    'goods'       => json_encode($item['goods'], JSON_UNESCAPED_UNICODE),
                    'live_status' => $item['live_status'],
                ];
                $room_info = model('weapp_live_room')->getInfo([['roomid', '=', $item['roomid']], ['site_id', '=', $site_id]], 'id');
                if (empty($room_info)) {
                    if (is_url($item['share_img'])) {
                        $pull_result = $upload->remotePull($item['share_img']);
                        $pull_result = $pull_result['data'];
                        if (isset($pull_result['pic_path']) && !empty($pull_result['pic_path'])) {
                            $data['share_img'] = $pull_result['pic_path'];
                        }
                    }
                    model('weapp_live_room')->add($data);
                } else {
                    $data = [
                        'goods'       => json_encode($item['goods'], JSON_UNESCAPED_UNICODE),
                        'live_status' => $item['live_status'],
                    ];
                    model('weapp_live_room')->update($data, [['id', '=', $room_info['id']]]);
                }
            }
            $total_page = ceil($result['data']['total'] / $limit);
            return $this->success(['page' => $start, 'total_page' => $total_page]);
        } else {
            return $this->success(['page' => $start, 'total_page' => 1]);
        }
    }
    /**
     * 获取直播间信息
     */
    public function getRoomInfo($condition = [], $field = '*')
    {
        $data = model('weapp_live_room')->getInfo($condition, $field);
        if (!empty($data)) {
            $data['status_name'] = $this->liveStatus[$data['live_status']] ?? '';
            if (isset($data['goods']) && !empty($data['goods'])) $data['goods'] = json_decode($data['goods'], true);
        }
        return $this->success($data);
    }
    /**
     * 添加商品到直播间
     * @param $site_id
     * @param $room_id
     * @param $data
     */
    public function addGoods($site_id, $room_id, $data)
    {
        if (empty($data)) return $this->error('', '请先选择要添加的商品');
        $room_info = model('weapp_live_room')->getInfo([['site_id', '=', $site_id], ['roomid', '=', $room_id]], 'goods');
        if (empty($room_info)) return $this->error('', '未查找到直播间信息');
        $data = json_decode($data, true);
        $goods_ids  = [];
        $goods_data = [];
        foreach ($data as $item) {
            array_push($goods_ids, $item['goods_id']);
            array_push($goods_data, [
                'name'      => $item['name'],
                'cover_img' => $item['cover_img'],
                'url'       => $item['url'],
                'price'     => $item['price']
            ]);
        }
        $live   = new Live($site_id);
        $result = $live->roomAddGoods($room_id, $goods_ids);
        if ($result['code'] < 0) return $result;
        if (!empty($room_info['goods'])) {
            $room_goods = json_decode($room_info['goods'], true);
            $goods_data = array_merge($room_goods, $goods_data);
        }
        $res = model('weapp_live_room')->update(['goods' => json_encode($goods_data, JSON_UNESCAPED_UNICODE)], [['site_id', '=', $site_id], ['roomid', '=', $room_id]]);
        return $this->success($res);
    }
    /**
     * 轮询更新直播间状态
     * @param $site_id
     */
    public function updateRoomStatus($site_id)
    {
        $count = model('weapp_live_room')->getCount([['site_id', '=', $site_id], ['live_status', 'in', ['101', '102', '105']]]);
        if ($count) {
            $start  = 0;
            $result = $this->syncLiveRoom($start, 20, $site_id);
            if (isset($result['code']) && $result['code'] == 0 && $result['total_page'] > 1) {
                for ($i = 1; $i < $result['data']; $i++) {
                    $this->syncLiveRoom($i, 20, $site_id);
                }
            }
        }
    }
    /**
     * 删除直播间
     * @param $site_id
     * @param $room_ids
     */
    public function deleteRoom($site_id, $room_ids){
        $res = model('weapp_live_room')->delete([['site_id', '=', $site_id], ['id', 'in', $room_ids]]);
        return $this->success($res);
    }
}