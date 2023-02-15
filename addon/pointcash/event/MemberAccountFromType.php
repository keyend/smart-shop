<?php
namespace addon\pointcash\event;
/**
 * 会员账户变化来源类型
 */
class MemberAccountFromType
{
    public function handle($data)
    {
        $from_type = [
            'point' => [
                'pointcash' => [
                    'type_name' => '积分抵现',
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