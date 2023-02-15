<?php
namespace addon\agencyteam\event;
use think\facade\Log;
use addon\agencyteam\model\AgencyTeamModel;

class UpdateMemberLevel
{
    public function handle($data)
    {
        if (!empty($data)) {
            // 升级为非普通会员
            if ($data['level']['level_id'] > 1) {
                try {
                    $model = app()->make(AgencyTeamModel::class);
                    $parent_id = $model->where('member_id', $data['member_id'])->value('parent_id');
                    if ($parent_id != 0) {
                        $member = $model->where('member_id', $parent_id)->find();
                        if ($member) {
                            $member->auto_upgrade_team_level($data['level']['level_id']);
                        }
                    }
                } catch(\Exception $e) {
                    Log::error('[晋升团队失败]' . $e->getMessage());
                }
            }
        }
    }
}
