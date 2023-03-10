<?php
namespace addon\turntable\event;
/**
 * 会员账户变化来源类型
 */
class MemberAccountFromType
{
    public function handle($data)
    {
        $from_type = [
            'point'   => [
                'turntable' => [
                    'type_name' => '幸运抽奖',
                    'type_url'  => '',
                ],
            ],
            'balance' => [
                'turntable' => [
                    'type_name' => '幸运抽奖',
                    'type_url'  => '',
                ],
            ]
        ];
        if ($data == '') {
            return $from_type;
        } else {
            if (isset($from_type[$data])) {
                return $from_type[$data];
            } else {
                return [];
            }
        }
    }
}