<?php
namespace addon\memberconsume\event;
/**
 * 会员账户变化来源类型
 */
class MemberAccountFromType
{
    public function handle($data)
    {
        $from_type = [
            'point'  => [
                'order' => [
                    'type_name' => '消费',
                    'admin_url' => 'admin/order/detail',
                    'tag_name'  => 'order_id'
                ],
            ],
            'growth' => [
                'order' => [
                    'type_name' => '消费',
                    'admin_url' => 'admin/order/detail',
                    'tag_name'  => 'order_id'
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