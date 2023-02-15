<?php
namespace addon\topic\event;
use addon\topic\model\Topic;
/**
 * 关闭活动
 */
class CloseTopic
{
    public function handle($params)
    {
        $topic = new Topic();
        $res   = $topic->cronCloseTopic($params['relate_id']);
        return $res;
    }
}