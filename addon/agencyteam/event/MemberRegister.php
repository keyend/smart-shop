<?php
namespace addon\agencyteam\event;
use addon\agencyteam\model\AgencyTeamModel;
use app\job\EntityMQService;
/**
 * 活动展示
 */
class MemberRegister
{
    /**
     * 会员注册
     * @param unknown $order
     * @return multitype:
     */
    public function handle($param)
    {
        if (isset($param['member_id']) && !empty($param['member_id'])) {
            EntityMQService::push(__CLASS__, ['handleRegister', $param]);
        }
    }

    /**
     * 会员注册时顺便把团队加入
     *
     * @param array $param
     * @return void
     */
    public function handleRegister($param = [])
    {
        $model = app()->make(AgencyTeamModel::class);
        $member_exist = $model->where('member_id', $param['member_id'])->find();

        if (!$member_exist) {
            $member_model = model('member');
            $member_info = $member_model->getInfo([['member_id', '=', $param['member_id']]], 'member_id,source_member');
            $parent_info = $member_model->getInfo([['member_id', '=', $member_info['source_member']]], 'member_id,username');
            $data = [
                'site_id' => $param['site_id'],
                'member_id' => $param['member_id'],
                'parent_id' => $member_info['source_member'],
                'parent_username' => $parent_info['username'],
            ];
            $model->insert($data);
        } else {
            $member_info = $param;
        }

        $model->clearRedundancy();
        $model->accumulation($member_info, 1);
    }
}