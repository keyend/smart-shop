<?php
namespace addon\groupbuy\model;
use app\model\BaseModel;
use extend\Poster as PosterExtend;
/**
 * 海报生成类
 */
class Poster extends BaseModel
{
    /**
     * 商品海报
     */
    public function goods($app_type, $page, $qrcode_param, $promotion_type = 'null', $site_id)
    {
        try {
            $goods_info = $this->getGoodsInfo($qrcode_param['id']);
            $img_arr = explode(",", $goods_info['goods_image']);
            
            if (empty($goods_info)) return $this->error('未获取到商品信息');
            $qrcode_info = $this->getGoodsQrcode($app_type, $page, $qrcode_param, $promotion_type, $site_id);
            if ($qrcode_info['code'] < 0) return $qrcode_info;
            if (!empty($qrcode_param['source_member'])) {
                $member_info = $this->getMemberInfo($qrcode_param['source_member']);
            }
            $poster = new PosterExtend(740, 1120);
            $option = [
                [
                    'action' => 'imageCopy', // 背景图
                    'data'   => [
                        'upload/poster/bg/promotion_2.png',
                        0,
                        0,
                        740,
                        1120
                    ]
                ],
                [
                    'action' => 'imageCopy', // 商品图
                    'data'   => [
                        $img_arr[0],
                        100,
                        134,
                        539,
                        539,
                        5
                    ]
                ],
                [
                    'action' => 'imageCopy', // 条幅
                    'data'   => [
                        'upload/poster/bg/groupbuy.png',
                        100,
                        618,
                        539,
                        55
                    ]
                ],
                [
                    'action' => 'imageCopy', // 二维码
                    'data'   => [
                        $qrcode_info['data']['path'],
                        320,
                        865,
                        100,
                        100,
                    ]
                ],
                [
                    'action' => 'imageText', // 写入商品价格
                    'data'   => [
                        '¥' . $goods_info['groupbuy_price'],
                        25,
                        [255, 95, 75],
                        120,
                        733,
                        500,
                        2,
                        true
                    ]
                ],
                [
                    'action' => 'imageText', // 写入商品名称
                    'data'   => [
                        $goods_info['goods_name'],
                        18,
                        [89, 89, 89],
                        120,
                        770,
                        500,
                        1,
                        true
                    ]
                ]
            ];
            if (!empty($member_info)) {
                $member_option = [
                    [
                        'action' => 'imageCircularCopy', // 写入用户头像
                        'data'   => [
                            !empty($member_info['headimg']) ? $member_info['headimg'] : 'upload/uniapp/default_headimg.png',
                            100,
                            35,
                            80,
                            80
                        ]
                    ],
                    [
                        'action' => 'imageText', // 写入分享人昵称
                        'data'   => [
                            $member_info['nickname'],
                            22,
                            [255, 255, 255],
                            200,
                            75,
                            440,
                            1
                        ]
                    ],
                    [
                        'action' => 'imageText', // 写入分享语
                        'data'   => [
                            '',
                            18,
                            [255, 255, 255],
                            200,
                            102,
                            440,
                            1
                        ]
                    ]
                ];
                $option        = array_merge($option, $member_option);
            }
            $option_res = $poster->create($option);
            if (is_array($option_res)) return $option_res;
            $res = $option_res->jpeg('upload/poster/goods', 'goods_' . $promotion_type . '_' . $qrcode_param['id'] . '_' . $qrcode_param['source_member'] . '_' . $app_type);
            return $res;
        } catch (\Exception $e) {
            return $this->error($e->getMessage() . $e->getFile() . $e->getLine());
        }
    }
    /**
     * 获取用户信息
     * @param unknown $member_id
     */
    private function getMemberInfo($member_id)
    {
        $info = model('member')->getInfo(['member_id' => $member_id], 'nickname,headimg');
        return $info;
    }
    /**
     * 获取商品信息
     * @param unknown $sku_id
     */
    private function getGoodsInfo($id)
    {
        $info = model('promotion_groupbuy')->getInfo(['groupbuy_id' => $id]);
        return $info;
    }
    /**
     * 获取商品二维码
     * @param unknown $app_type 请求类型
     * @param unknown $page uniapp页面路径
     * @param unknown $qrcode_param 二维码携带参数
     * @param string $promotion_type 活动类型 null为无活动
     */
    private function getGoodsQrcode($app_type, $page, $qrcode_param, $promotion_type = 'null', $site_id)
    {
        $res = event('Qrcode', [
            'site_id'     => $site_id,
            'app_type'    => $app_type,
            'type'        => 'create',
            'data'        => $qrcode_param,
            'page'        => $page,
            'qrcode_path' => 'upload/qrcode/goods',
            'qrcode_name' => 'goods_' . $promotion_type . '_' . $qrcode_param['id'] . '_' . $qrcode_param['source_member'] . '_' . $site_id,
        ], true);
        return $res;
    }
}