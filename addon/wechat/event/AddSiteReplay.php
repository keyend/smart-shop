<?php
namespace addon\wechat\event;
use addon\wechat\model\Replay;
/**
 * 增加站点关注回复
 */
class AddSiteReplay
{
    public function handle($param)
    {
        if (!empty($param['site_id'])) {
            $replay = new Replay();
            $data   = [
                'site_id'       => $param['site_id'],
                'rule_name'     => '关注回复',
                'rule_type'     => 'AFTER',
                'keywords_json' => '',
                'replay_json'   => '[{"type":"text","reply_content":"关注回复"}]',
                'create_time'   => time()
            ];
            $res    = $replay->addRule($data);
            return $res;
        }
    }
}