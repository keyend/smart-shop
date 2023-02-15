<?php
namespace addon\cards\event;
use app\model\games\Games;
/**
 * 关闭活动
 */
class CloseCards
{
    public function handle($params)
    {
        $games = new Games();
        $res   = $games->cronCloseGames($params['relate_id']);
        return $res;
    }
}