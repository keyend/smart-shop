<?php
namespace addon\agencyteam\event;

class ShowPromotion
{
    public function handle()
    {
        $data = [
            'shop' => [
                [
                    'name'        => 'agencyteam',
                    'show_type'   => 'tool',
                    'title'       => '代理团队',
                    'description' => '按区域进行代理管理团队成员',
                    'icon'        => 'addon/agencyteam/icon.jpg',
                    'url'         => 'agencyteam://shop/index/index',
					'sort'        => '1',
                ]
            ]
        ];

        return $data;
    }
}