<?php
namespace app\event;
use app\model\member\MemberLevel;
/**
 * 增加默认会员等级
 */
class AddMemberLevel
{
    public function handle($param)
    {
        if (!empty($param['site_id'])) {
            $member_level = new MemberLevel();
            $data         = [
                'site_id'          => $param['site_id'],
                'level_name'       => '普通会员',
                'is_default'       => 1,
                'is_free_shipping' => 0
            ];
            $res          = $member_level->addMemberLevel($data);
            return $res;
        }
    }
}