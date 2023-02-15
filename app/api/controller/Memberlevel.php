<?php
namespace app\api\controller;
use app\model\member\MemberLevel as MemberLevelModel;
class Memberlevel extends BaseApi
{
    /**
     * 列表信息
     */
    public function lists()
    {
        $member_level_model = new MemberLevelModel();
        $member_level_list  = $member_level_model->getMemberLevelList([['site_id', '=', $this->site_id]], 'level_id,level_name,growth,remark,consume_discount,is_free_shipping,point_feedback,send_point,send_balance,send_coupon', 'growth asc');
        return $this->response($member_level_list);
    }
}