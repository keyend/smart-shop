<?php
namespace addon\membersignin\event;
/**
 * 会员操作
 */
class MemberAction
{
    /**
     * 会员操作
     */
    public function handle($data)
    {
        if ($data['member_action'] == 'memberregister') {
            return success();
        }
        return '';
    }
}