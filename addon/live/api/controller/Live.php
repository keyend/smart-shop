<?php
namespace addon\live\api\controller;
use addon\live\model\Room;
use app\api\controller\BaseApi;
/**
 * 直播
 */
class Live extends BaseApi
{
    /**
     * 获取直播间信息
     */
    public function info()
    {
        $room      = new Room();
        $condition = [
            'site_id'     => $this->site_id,
            'live_status' => '101'
        ];
        // 优先查询进行中的
        $room_info = $room->getRoomInfo($condition);
        if (empty($room_info['data'])) {
            $condition['live_status'] = '102';
            // 没有进行中的查询未开始的
            $room_info = $room->getRoomInfo($condition);
        }
        return $this->response($room_info);
    }
    /**
     * 获取直播间列表
     * @return false|string
     */
    public function page()
    {
        $page      = isset($this->params['page']) ? $this->params['page'] : 1;
        $page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
        $room = new Room();
        $data = $room->getRoomPageList(['site_id' => $this->site_id], '*', 'live_status asc', $page, $page_size);
        return $this->response($data);
    }
    /**
     * 修改直播间状态
     */
    public function modifyLiveStatus()
    {
        $room_id = $this->params['room_id'] ?? 0;
        $status  = $this->params['status'] ?? '';
        if (empty($status)) return $this->response($this->error());
        $room = new Room();
        $res  = $room->updateRoomInfo(['live_status' => $status], [['roomid', '=', $room_id], ['site_id', '=', $this->site_id]]);
        return $this->response($res);
    }
}