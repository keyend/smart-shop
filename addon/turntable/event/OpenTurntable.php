<?php
namespace addon\turntable\event;
use app\model\games\Games;
/**
 * 关闭活动
 */
class OpenTurntable
{
    public function handle($params)
    {
        $games = new Games();
        $res   = $games->cronOpenGames($params['relate_id']);
        return $res;
    }
}