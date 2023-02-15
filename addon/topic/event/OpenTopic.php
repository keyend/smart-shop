<?php
namespace addon\topic\event;
use addon\topic\model\Topic;
/**
 * 启动活动
 */
class OpenTopic
{
    public function handle($params)
    {
        $topic = new Topic();
        $res   = $topic->cronOpenTopic($params['relate_id']);
        return $res;
    }
}