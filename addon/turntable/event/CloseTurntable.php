<?php
namespace addon\turntable\event;
use app\model\games\Games;
/**
 * 关闭活动
 */
class CloseTurntable
{
    public function handle($params)
    {
        $games = new Games();
        $res   = $games->cronCloseGames($params['relate_id']);
        return $res;
    }
}