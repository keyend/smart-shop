<?php
namespace addon\egg\event;
use app\model\games\Games;
/**
 * 关闭活动
 */
class OpenEgg
{
    public function handle($params)
    {
        $games = new Games();
        $res   = $games->cronOpenGames($params['relate_id']);
        return $res;
    }
}