<?php
namespace addon\agencyteam\event;
use addon\agencyteam\model\AgencyTeamModel;
use app\job\EntityMQService;
/**
 * 会员注销
 */
class MemberCancel
{

    /**
     * @param unknown $order
     * @return multitype:
     */
    public function handle($param)
    {
        if (isset($param['member_id']) && !empty($param['member_id'])) {
            EntityMQService::push(__CLASS__, ['handleCancel', $param]);
        }
    }

    public function handleCancel($param)
    {
        $model = app()->make(AgencyTeamModel::class);
        $member_exist = $model->where('member_id', $param['member_id'])->field('id,member_id,site_id')->find();
        if ($member_exist) {
            $member_info = $member_exist->toArray();
            $model->accumulation($member_info, -1);
            $member_exist->delete();
        }
    }
}