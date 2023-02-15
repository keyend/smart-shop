<?php
namespace addon\cards\event;
use app\model\games\Games;
/**
 * 关闭活动
 */
class OpenCards
{
    public function handle($params)
    {
        $games = new Games();
        $res   = $games->cronOpenGames($params['relate_id']);
        return $res;
    }
}