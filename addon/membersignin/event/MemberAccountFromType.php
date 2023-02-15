<?php
namespace addon\membersignin\event;
/**
 * 会员账户变化来源类型
 */
class MemberAccountFromType
{
    public function handle($data)
    {
        $from_type = [
            'point'  => [
                'signin' => [
                    'type_name' => '签到',
                    'type_url'  => '',
                ],
            ],
            'growth' => [
                'signin' => [
                    'type_name' => '签到',
                    'type_url'  => '',
                ],
            ],
        ];
        if ($data == '') {
            return $from_type;
        } else {
            return $from_type[$data];
        }
    }
}