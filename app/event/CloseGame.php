<?php
namespace app\event;
use app\model\games\Games;
/**
 * 关闭物流查询
 * @author Administrator
 *
 */
class CloseGame
{
    public function handle($param)
    {
        $model  = new Games();
        $result = $model->cronCloseGames($param['relate_id']);
        return $result;
    }
}