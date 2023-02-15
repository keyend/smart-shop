<?php
namespace addon\egg\event;
use app\model\games\Games;
/**
 * 关闭活动
 */
class CloseEgg
{
    public function handle($params)
    {
        $games = new Games();
        $res   = $games->cronCloseGames($params['relate_id']);
        return $res;
    }
}