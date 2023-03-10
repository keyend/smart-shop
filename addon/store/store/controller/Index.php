<?php
namespace addon\store\store\controller;
use addon\store\model\StoreMember as StoreMemberModel;
/**
 * 门店首页
 * @author Administrator
 *
 */
class Index extends BaseStore
{
    public function Index()
    {
        $store_member_model = new StoreMemberModel();
        $member_count = $store_member_model->getMemberCount(
            [
                [ 'store_id', '=', $this->store_id ]
            ]
        );
        $this->assign('member_count', $member_count[ 'data' ]);
        return $this->fetch("index/index", [], $this->replace);
    }
}