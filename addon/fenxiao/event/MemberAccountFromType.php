<?php
namespace addon\fenxiao\event;
/**
 * 会员账户变化来源类型
 */
class MemberAccountFromType
{

    public function handle($data)
    {
        $from_type = [
            'balance_money' => [
                'fenxiao' => [
                    'type_name' => '分销佣金',
                    'type_url'  => '',
                ],
            ],
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