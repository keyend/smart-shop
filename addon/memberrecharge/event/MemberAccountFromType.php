<?php
namespace addon\memberrecharge\event;
/**
 * 会员账户变化来源类型
 */
class MemberAccountFromType
{
    public function handle($data)
    {
        $from_type = [
            'balance' => [
                'memberrecharge' => [
                    'type_name' => '会员充值',
                    'admin_url' => 'admin/order/detail',
                    'tag_name'  => 'order_id'
                ],
            ],
            'point'   => [
                'memberrecharge' => [
                    'type_name' => '会员充值',
                    'type_url'  => '',
                ],
            ],
            'growth'  => [
                'memberrecharge' => [
                    'type_name' => '会员充值',
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