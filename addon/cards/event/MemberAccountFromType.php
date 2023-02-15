<?php
namespace addon\cards\event;
/**
 * 会员账户变化来源类型
 */
class MemberAccountFromType
{
    public function handle($data)
    {
        $from_type = [
            'point'   => [
                'cards' => [
                    'type_name' => '刮刮乐',
                    'type_url'  => '',
                ],
            ],
            'balance' => [
                'cards' => [
                    'type_name' => '刮刮乐',
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