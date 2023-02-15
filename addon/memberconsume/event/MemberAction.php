<?php
namespace addon\memberconsume\event;
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
        if ($data['member_action'] == 'memberconsume') {
            return success();
        }
        return '';
    }
}