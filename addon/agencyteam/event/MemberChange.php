<?php
namespace addon\agencyteam\event;
use addon\agencyteam\model\AgencyTeamModel;
use app\job\EntityMQService;
/**
 * 会员变更
 */
class MemberChange
{
    /**
     * 变更触发事件
     *
     * [source_member => 新的推荐人, origin_source_member => 旧推荐人]
     * 
     * @param array $params
     * @return void
     */
    public function handle($params)
    {
        EntityMQService::push(__CLASS__, ['handleChange', $params]);
    }

    public function handleChange($params)
    {
        $team_model = app()->make(AgencyTeamModel::class);
        $member_info = $team_model->where('member_id', $params['member_id'])->find();
        if (empty($member_info)) {
            if (!isset($params['site_id'])) {
                $params['site_id'] = 1;
            }

            event("MemberRegister", $params);
            EntityMQService::push(__CLASS__, ['handleChange', $params], 1);
            return true;
        }

        if ($member_info['parent_id'] != $params['source_member']) {
            $current_member = model('member')->getInfo([['member_id', '=', $params['source_member']]], 'member_id,source_member,username');
            $origin_member_id = $member_info['parent_id'];

            if (!empty($current_member)) {
                $member_info->parent_id = $current_member['member_id'];
                $member_info->parent_username = $current_member['username'];
                $member_info->save();

                $team_model->accumulation($current_member, 1);

                $origin_member = model('member')->getInfo([['member_id', '=', $origin_member_id]], 'member_id,source_member');
                if (!empty($origin_member)) {
                    $team_model->accumulation($origin_member, -1);
                }
            }
        }
    }
}