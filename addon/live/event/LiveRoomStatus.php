<?php
namespace addon\live\event;
use addon\live\model\Room;
/**
 * 轮询更新直播间状态
 */
class LiveRoomStatus
{
    /**
     * 轮询更新直播间状态
     *
     * @return multitype:number unknown
     */
    public function handle($param)
    {
        $room = new Room();
        $room->updateRoomStatus($param['relate_id']);
    }
}