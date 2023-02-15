<?php
namespace addon\fenxiao\event;
use app\model\web\DiyView as DiyViewModel;

/**
 * 增加默认自定义数据：分销市场
 */
class AddSiteDiyView
{

    public function handle($param)
    {
        if (!empty($param['site_id'])) {

            $diy_view_model = new DiyViewModel();

            $data = [
                [
                    'site_id' => $param['site_id'],
                    'title'   => '分销市场',
                    'name'    => "DIY_FENXIAO_MARKET",
                    'type'    => "shop",
                    'value'   => json_encode([
                        "global" => [
                            "title"         => "分销市场",
                            "openBottomNav" => false,
                            "bgColor"       => "#ffffff",
                            "bgUrl"         => "",
                            "moreLink"      =>[],
                            "mpCollect"     => false,
                            "navStyle"      => 1,
                            "popWindow"     => [
                                "imageUrl"  => "",
                                "count"     => -1,
                                "link"      => [],
                                "imgWidth"  => "",
                                "imgHeight" => ""
                            ],
                            "textImgPosLink"   => "left",
                            "textImgStyleLink" => "1",
                            "textNavColor"     => "#333333",
                            "topNavColor"      => "#ffffff",
                            "topNavImg"        => "",
                            "topNavbg"         => false
                        ],
                        "value"  => [
                            [
                                "selectedTemplate" => "carousel-posters",
                                "imageClearance"   => 0,
                                "imageRadius"      => "right-angle",
                                "carouselChangeStyle"=> "circle",
                                "marginTop"        => 0,
                                "padding"          => 0,
                                "height"           => 150,
                                "list"             => [
                                    [
                                        "imageUrl" => "upload/default/diy_view/fenxiao_market_gg.png",
                                        "title"    => "",
                                        "link"     => [],
                                        "imgWidth" => 0,
                                        "imgHeight"=> 0
                                    ]
                                ],
                                "addon_name"       => "",
                                "type"             => "IMAGE_ADS",
                                "name"             => "图片广告",
                                "controller"       => "ImageAds"
                            ],
                            [
                                "selectedTemplate" => "carousel-posters",
                                "imageClearance"   => 0,
                                "imageRadius"      => "right-angle",
                                "carouselChangeStyle"=> "circle",
                                "marginTop"        => 0,
                                "padding"          => 0,
                                "height"           => 58,
                                "list"             => [
                                    [
                                        "imageUrl" => "upload/default/diy_view/fenxiao_market_gg2.png",
                                        "title"    => "",
                                        "link"     => [],
                                        "imgWidth" => 0,
                                        "imgHeight"=> 0
                                    ]
                                ],
                                "addon_name"       => "",
                                "type"             => "IMAGE_ADS",
                                "name"             => "图片广告",
                                "controller"       => "ImageAds"
                            ],
                            [
                                "selectedTemplate"       => "row1-lt-of2-rt",
                                "list"                   => [
                                    [
                                        "imageUrl" => "upload/default/diy_view/fenxiao_market_cube_1.png",
                                        "link"     => []
                                    ],
                                    [
                                        "imageUrl" => "upload/default/diy_view/fenxiao_market_cube_2.png",
                                        "link"     => []
                                    ],
                                    [
                                        "imageUrl" => "upload/default/diy_view/fenxiao_market_cube_3.png",
                                        "link"     => []
                                    ]
                                ],
                                "selectedRubikCubeArray" => [],
                                "diyHtml"                => "",
                                "customRubikCube"        => 4,
                                "heightArray"            => [
                                    "74.25px",
                                    "59px",
                                    "48.83px",
                                    "41.56px"
                                ],
                                "imageGap"               => 0,
                                "addon_name"             => "",
                                "type"                   => "RUBIK_CUBE",
                                "name"                   => "魔方",
                                "backgroundColor"        => "",
                                "controller"             => "RubikCube"
                            ],
                            [
                                "sources"               => "default",
                                "categoryId"            => 0,
                                "goodsCount"            => 6,
                                "goodsId"               => [],
                                "style"                 => 1,
                                "backgroundColor"       => "",
                                "padding"               => 0,
                                "list"                  => [
                                    "imageUrl" => "",
                                    "title"    => "分销商品"
                                ],
                                "listMore"              => [
                                    "imageUrl" => "",
                                    "title"    => "查看更多"
                                ],
                                "moreTextColor"         => "#858585",
                                "addon_name"            => "fenxiao",
                                "type"                  => "FENXIAO_GOODS_LIST",
                                "name"                  => "分销商品",
                                "controller"            => "FenxiaoGoodsList",
                                "titleTextColor"        => "#000",
                                "defaultTitleTextColor" => "#000",
                                "defaultMoreTextColor"  => "#858585"
                            ]

                        ]
                    ])
                ]
            ];

            $res = $diy_view_model->addSiteDiyViewList($data);

            return $res;

        }

    }

}