<?php
namespace app\event;
use app\model\games\Games;
/**
 * 关闭物流查询
 * @author Administrator
 *
 */
class OpenGame
{
    public function handle($param)
    {
        $model  = new Games();
        $result = $model->cronOpenGames($param['relate_id']);
        return $result;
    }
}