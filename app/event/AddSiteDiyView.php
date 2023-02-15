<?php
namespace app\event;
use app\model\web\DiyView as DiyViewModel;
/**
 * 增加默认自定义数据：网站主页、商品分类、底部导航
 */
class AddSiteDiyView
{
    public function handle($param)
    {
        if (!empty($param[ 'site_id' ])) {
            $diy_view_model = new DiyViewModel();
            // 添加自定义主页装修
            $value = json_encode([
                "global" => [
                    "bgColor" => "",
                    "title" => "商城首页",
                    "openBottomNav" => false,
                    "bgUrl" => "",
                    "popWindow" => [
                        "imageUrl" => "upload/default/diy_view/adv_supernatant.png",
                        "count" => 1,
                        "link" => [
                            "name" => ""
                        ]
                    ]
                ],

                "value" => [
                    [
                        "addon_name" => "",
                        "type" => "SEARCH",
                        "name" => "商品搜索",
						"title" => "商品搜索",
                        "controller" => "Search",
                        "textColor" => "#bbbbbb",
                        "backgroundColor" => "#ffffff",
                        "bgColor" => "#f7f7f7",
                        "borderType" => 2,
                        "textAlign" => "left",
                        "defaultTextColor" => "#999999"
                    ],
					[
                        "height" => 10,
                        "backgroundColor" => "",
                        "addon_name" => "",
                        "type" => "HORZ_BLANK",
                        "name" => "辅助空白",
                        "controller" => "HorzBlank",
                        "marginLeftRight" => 0
                    ],
                    [
                        "selectedTemplate" => "carousel-posters",
                        "imageClearance" => 0,
                        "padding" => 0,
                        "height" => 0,
                        "list" => [
                            [
                                "imageUrl" => "upload/default/diy_view/posters_1.png",
                                "title" => "",
                                "link" => [
                                    "name" => ""
                                ]
                            ],
                            [
                                "imageUrl" => "upload/default/diy_view/posters_2.png",
                                "title" => "",
                                "link" => [
                                    "name" => ""
                                ]
                            ]
                        ],
                        "addon_name" => "",
                        "type" => "IMAGE_ADS",
                        "name" => "图片广告",
                        "controller" => "ImageAds"
                    ],
												
                    [
                        "textColor" => "#666666",
                        "backgroundColor" => "#ffffff",
                        "selectedTemplate" => "imageNavigation",
                        "scrollSetting" => "fixed",
                        "imageScale" => "70",
                        "padding" => 20,
                        "list" => [
                            [
                                "imageUrl" => "upload/default/diy_view/nav/pintuan.png",
                                "title" => "拼团",
                                "link" =>  [
                                    "id" => 2803,
                                    "addon_name" =>  "pintuan",
                                    "name" =>  "PINTUAN_LIST",
                                    "title" =>  "拼团列表",
                                    "web_url" =>  "",
                                    "wap_url" =>  "/promotionpages/pintuan/list/list",
                                    "icon" =>  "",
                                    "selected" =>  false,
                                    "addon_title" =>  "拼团",
                                    "addon_icon" =>  "addon/pintuan/icon.png"									
                                ]
                            ],
                            [
								"imageUrl" => "upload/default/diy_view/nav/group_buy.png",
                				"title" => "团购",
                				"link" => [
                                    "id" => 2804,
                                    "addon_name" => "groupbuy",
                                    "name" => "GROUPBUY_LIST",
                                    "title" => "团购列表",
                                    "web_url" => "",
                                    "wap_url" => "/promotionpages/groupbuy/list/list",
                                    "icon" => "",
                                    "selected" => false,
                                    "addon_title" => "团购",
                                    "addon_icon" => "addon/groupbuy/icon.png"
                                ]
                            ],
                            [
                                "imageUrl" => "upload/default/diy_view/nav/seckill.png",
                                "title" => "秒杀",
                                "link" => [
                                    "id" => 2802,
                                    "addon_name" => "seckill",
                                    "name" => "SECKILL_LIST",
                                    "title" => "秒杀列表",
                                    "web_url" => "",
                                    "wap_url" => "/promotionpages/seckill/list/list",
                                    "icon" => "",
                                    "selected" => false,
                                    "addon_title" => "限时秒杀",
                                    "addon_icon" => "addon/seckill/icon.png"									
                                ]
                            ],
                            [
                                "imageUrl" => "upload/default/diy_view/nav/bargain.png",
                                "title" => "砍价",
                                "link" => [
                                   "id" => 3186,
                                   "addon_name" => "bargain",
                                   "addon_title" => "砍价",
                                   "name" => "BARGAIN_LIST",
                                   "title" => "砍价列表",
                                   "web_url" => "",
                                   "wap_url" => "/promotionpages/bargain/list/list",
                                   "icon" => "",
                                   "addon_icon" => "addon/bargain/icon.png",
                                   "selected" => false										
                                ]
                            ],							
                            [
                                "imageUrl" => "upload/default/diy_view/nav/live.png",
								"title" => "直播",
                                "link" => [
                                   "name" => "LIVE_LIST",
                                   "id" => 2898,
                                   "addon_name" => "live",
                                   "addon_title" => "小程序直播",
                                   "title" => "直播",
                                   "web_url" => "",
                                   "wap_url" => "/otherpages/live/list/list",
                                   "icon" => "",
                                   "addon_icon" => "addon/live/icon.png",
                                   "selected" => false																	
                                ]
                            ]
                        ],
                        "addon_name" => "",
                        "type" => "GRAPHIC_NAV",
                        "name" => "图文导航",
                        "controller" => "GraphicNav",
                        "paddingTopBottom" => 10,
                        "paddingLeftRight" => 10,
                        "marginTopBottom" => 0,
                        "marginLeftRight" => 0,
                        "defaultTextColor" => "#666666",
                        "borderTopLeftRadius" => 0,
                        "borderTopRightRadius" => 0,
                        "borderBottomLeftRadius" => 0,
                        "borderBottomRightRadius" => 0
                    ],		
					
                    [
                        "textColor" => "#666666",
                        "backgroundColor" => "#ffffff",
                        "selectedTemplate" => "imageNavigation",
                        "scrollSetting" => "fixed",
                        "imageScale" => "70",
                        "padding" => 20,
                        "list" => [
                            [
                                "imageUrl" => "upload/default/diy_view/nav/point.png",
                                "title" => "积分商城",
                                "link" => [
                                    "id" => 2869,
                                    "addon_name" => "",
                                    "name" => "POINT_INDEX",
                                    "title" => "积分商城",
                                    "web_url" => "",
                                    "wap_url" => "/promotionpages/point/list/list",
                                    "icon" => "",
                                    "selected" => false,
                                    "addon_title" => null,
                                    "addon_icon" => null	
                                ]                               							                                
                            ],		
                            [
	                            "imageUrl" => "upload/default/diy_view/nav/coupon.png",
								"title" => "优惠券",
                                "link" => [
                                    "name" => "COUPON_LIST",
                                    "id" => 2870,
                                    "addon_name" => "",
                                    "addon_title" => null,
                                    "title" => "领券中心",
                                    "web_url" => "",
                                    "wap_url" => "/otherpages/goods/coupon/coupon",
                                    "icon" => "",
                                    "addon_icon" => null,
                                    "selected" => false														
                                ]
                            ],							
                            [
	                            "imageUrl" => "upload/default/diy_view/nav/topic.png",
                                "title" => "专题活动",
                                "link" => [
                                    "id" => 2871,
                                    "addon_name" => "",
                                    "name" => "TOPIC_LIST",
                                    "title" => "专题活动",
                                    "web_url" => "",
                                    "wap_url" => "/promotionpages/topics/list/list",
                                    "icon" => "",
                                    "selected" => false,
                                    "addon_title" => null,
                                    "addon_icon" => null						
                                ]
                            ],
                            [
	                            "imageUrl" => "upload/default/diy_view/nav/signin.png",
                                "title" => "积分签到",
                                "link" => [
                                    "id" => 2868,
                                    "addon_name" => "",
                                    "name" => "SIGNIN_LIST",
                                    "title" => "积分签到",
                                    "web_url" => "",
                                    "wap_url" => "/otherpages/member/signin/signin",
                                    "icon" => "",
                                    "selected" => false,
                                    "addon_title" => null,
                                    "addon_icon" => null						
                                ]
                            ],								
                            [
	                            "imageUrl" => "upload/default/diy_view/nav/category.png",
                                "title" => "全部分类",
                                "link" => [
                                    "id" => 2867,
                                    "addon_name" => "",
                                    "name" => "CATEGORY_LIST",
                                    "title" => "全部分类",
                                    "web_url" => "",
                                    "wap_url" => "/pages/goods/category/category",
                                    "icon" => "",
                                    "selected" => false,
                                    "addon_title" => null,
                                    "addon_icon" => null															
                                ]
                            ]
                        ],
                        "addon_name" => "",
                        "type" => "GRAPHIC_NAV",
                        "name" => "图文导航",
                        "controller" => "GraphicNav",
                        "paddingTopBottom" => 10,
                        "paddingLeftRight" => 10,
                        "marginTopBottom" => 0,
                        "marginLeftRight" => 0,
                        "defaultTextColor" => "#666666",
                        "borderTopLeftRadius" => 0,
                        "borderTopRightRadius" => 0,
                        "borderBottomLeftRadius" => 0,
                        "borderBottomRightRadius" => 0								
                    ],
					[					
                        "color" => "#e5e5e5",
                        "defaultColor" => "#e5e5e5",
                        "margin" => 0,
                        "borderStyle" => "solid",
                        "padding" => "no-padding",
                        "addon_name" => "",
                        "type" => "HORZ_LINE",
                        "name" => "辅助线",
                        "controller" => "HorzLine",
                        "is_delete" => "0"									
					],					
					
                    [
                        "list" => [
                            [
                                "link" => [
                                    "name" => ""
                                ],
                                "title" => "新零售社交电商系统上线啦"
                            ]
                        ],
                        "addon_name" => "",
                        "type" => "NOTICE",
                        "name" => "公告",
                        "controller" => "Notice",
                        "backgroundColor" => "#ffffff",
                        "sources" => "default",
                        "paddingLeftRight" => 0,
                        "paddingTopBottom" => 0,
                        "style" => 1,
                        "isEdit" => 1,
                        "textColor" => "#333333",
                        "fontSize" => 14,
                        "noticeIds" => [],
                        "defaultTextColor" => "#333333"
                    ],	
					
					[					
                        "color" => "#e5e5e5",
                        "defaultColor" => "#e5e5e5",
                        "margin" => 0,
                        "borderStyle" => "solid",
                        "padding" => "no-padding",
                        "addon_name" => "",
                        "type" => "HORZ_LINE",
                        "name" => "辅助线",
                        "controller" => "HorzLine",
                        "is_delete" => "0"									
					],
								
                    [
                        "height" => 10,
                        "backgroundColor" => "#ffffff",
                        "addon_name" => "",
                        "type" => "HORZ_BLANK",
                        "name" => "辅助空白",
                        "controller" => "HorzBlank",
                        "marginLeftRight" => 0
                    ],					
			
                    [
                        "backgroundColor" => "#ffffff",
                        "addon_name" => "",
                        "type" => "GRAPHIC_NAV",
                        "name" => "图文导航",
                        "controller" => "GraphicNav",
                        "textColor" => "#666666",
                        "defaultTextColor" => "#666666",
                        "selectedTemplate" => "imageNavigation",
                        "scrollSetting" => "fixed",
                        "imageScale" => 100,
                        "padding" => 0,
                        "paddingTopBottom" => 10,
                        "paddingLeftRight" => 0,
                        "list" => [
                            [
                                "imageUrl" => "upload/default/diy_view/adv_01.png",
                                "link" => [
                                    "name" => ""
                                ],
                            "title" => ""
                            ]
                        ],
                        "borderTopLeftRadius" => 0,
                        "borderTopRightRadius" => 0,
                        "borderBottomLeftRadius" => 0,
                        "borderBottomRightRadius" => 0,
                        "marginTopBottom" => 0,
                        "marginLeftRight" => 0
                    ],					
					
                    [
                        "addon_name" => "",
                        "type" => "RUBIK_CUBE",
                        "name" => "魔方",
                        "controller" => "RubikCube",
                        "backgroundColor" => "#ffffff",
                        "list" => [
                            [
                                "link" => [
                                  "name" => ""
                            ],
                                "imageUrl" => "upload/default/diy_view/adv_02.png"
                                ],
                            [
                                "link" => [
                                  "name" => ""
                                ],
                                "imageUrl" => "upload/default/diy_view/adv_03.png"
                                ],
                            [
                                "imageUrl" => "upload/default/diy_view/adv_04.png",
                                "link" => [
                                  "name" => ""
                                ]
                            ]
                        ],
                            "selectedTemplate" => "row1-lt-of2-rt",
                            "selectedRubikCubeArray" => [],
                            "diyHtml" => "",
                            "customRubikCube" => 4,
                            "heightArray" => [
                            "74.25px",
                            "59px",
                            "48.83px",
                            "41.56px"
                        ],
                        "imageGap" => 15
                    ],	
					
                    [
                        "style" => 2,
                        "backgroundColor" => "ffffff",
                        "padding" => 10,
                        "addon_name" => "coupon",
                        "type" => "COUPON",
                        "name" => "优惠券",
                        "controller" => "Coupon",
                        "sources" => "default",
                        "couponCount" => "6",
                        "status" => 1,
                        "couponIds" => []
                    ],
                    [
                        "style" => "10",
                        "backgroundColor" => "ffffff",
                        "addon_name" => "",
                        "type" => "TEXT",
                        "name" => "文本",
                        "controller" => "Text",
                        "title" => "精品推荐",
                        "subTitle" => "",
                        "padding" => 0,
                        "link" => [
                            "name" => ""
                        ],
                        "fontSize" => 16,
                        "fontSizeSub" => 14,
                        "colorSub" => "#999",
                        "sub" => "1",
                        "isShowMore" => 0,
                        "fontWeight" => 600,
                        "moreText" => "查看更多",
                        "moreLink" => [],
                        "btnColor" => "#999",
                        "textColor" => "#333333",
                        "defaultTextColor" => "#333333",
                        "defaultColorSub" => "#999",
                        "defaultBtnColor" => "#999"
                    ],
                    [
                        "sources" => "default",
                        "categoryId" => 0,
                        "goodsCount" => "6",
                        "goodsId" => [],
                        "style" => 1,
                        "backgroundColor" => "",
                        "paddingUpDown" => 0,
                        "paddingLeftRight" => 0,
                        "isShowCart" => 0,
                        "cartStyle" => 1,
                        "isShowGoodName" => 1,
                        "isShowMarketPrice" => 1,
                        "isShowGoodSaleNum" => 1,
                        "isShowGoodSubTitle" => 1,
                        "addon_name" => "",
                        "type" => "GOODS_LIST",
                        "name" => "商品列表",
                        "controller" => "GoodsList"
                    ]
                ]
            ]);
            // 网站主页
            $data = [
                [
                    'site_id' => $param[ 'site_id' ],
                    'title' => '网站主页',
                    'name' => 'DIYVIEW_INDEX',
                    'type' => 'shop',
                    'value' => $value
                ],
                [
                    'site_id' => $param[ 'site_id' ],
                    'title' => '商品分类',
                    'name' => "DIYVIEW_GOODS_CATEGORY",
                    'type' => "shop",
                    'value' => json_encode([
                        "global" => [
                            "title" => "商品分类",
                            "openBottomNav" => false,
                            "bgColor" => "#ffffff",
                            "bgUrl" => ""
                        ],
                        "value" => [
                            [
                                "addon_name" => "",
                                "type" => "GOODS_CATEGORY",
                                "name" => "商品分类",
                                "controller" => "GoodsCategory",
                                "level" => 3,
                                "template" => 2
                            ]
                        ]
                    ])
                ]
            ];
            $res = $diy_view_model->addSiteDiyViewList($data);
            $diy_view_bottom_nav = [
                "type" => 1,
                "backgroundColor" => "#ffffff",
                "textColor" => "#666666",
                "textHoverColor" => "#fa0036",
                "bulge" => true,
                "list" => [
                    [
                        "iconPath" => "upload/default/diy_view/bottom/index.png",
                        "selectedIconPath" => "upload/default/diy_view/bottom/index_selected.png",
                        "text" => "首页",
                        "link" => [
                            "addon_name" => "",
                            "addon_title" => null,
                            "name" => "INDEX",
                            "title" => "主页",
                            "web_url" => "",
                            "wap_url" => "/pages/index/index/index",
                            "icon" => "",
                            "addon_icon" => null,
                            "selected" => false,
                            "type" => 0
                        ]
                    ],
                    [
                        "iconPath" => "upload/default/diy_view/bottom/category.png",
                        "selectedIconPath" => "upload/default/diy_view/bottom/category_selected.png",
                        "text" => "分类",
                        "link" => [
                            "addon_name" => "",
                            "addon_title" => null,
                            "name" => "GOODS_CATEGORY",
                            "title" => "商品分类",
                            "web_url" => "",
                            "wap_url" => "/pages/goods/category/category",
                            "icon" => "",
                            "addon_icon" => null,
                            "selected" => false
                        ]
                    ],
                    [ "iconPath" => "upload/default/diy_view/bottom/fenxiao.png",
                        "selectedIconPath" => "upload/default/diy_view/bottom/fenxiao_selected.png",
                        "text" => "分销中心",
                        "link" => [
                            "addon_name" => "",
                            "addon_title" => null,
                            "name" => "FENXIAO_CART",
                            "title" => "分销中心",
                            "web_url" => "",
                            "wap_url" => "/otherpages/fenxiao/index/index",
                            "icon" => "",
                            "addon_icon" => null,
                            "selected" => false
                        ],
                    ],					
                    [ "iconPath" => "upload/default/diy_view/bottom/cart.png",
                        "selectedIconPath" => "upload/default/diy_view/bottom/cart_selected.png",
                        "text" => "购物车",
                        "link" => [
                            "addon_name" => "",
                            "addon_title" => null,
                            "name" => "GOODS_CART",
                            "title" => "购物车",
                            "web_url" => "",
                            "wap_url" => "/pages/goods/cart/cart",
                            "icon" => "",
                            "addon_icon" => null,
                            "selected" => false
                        ],
                    ],
                    [ "iconPath" => "upload/default/diy_view/bottom/member_index.png",
                        "selectedIconPath" => "upload/default/diy_view/bottom/member_index_selected.png",
                        "text" => "我的",
                        "link" => [
                            "addon_name" => "",
                            "addon_title" => null,
                            "name" => "MEMBER_INDEX",
                            "title" => "会员中心",
                            "web_url" => "",
                            "wap_url" => "/pages/member/index/index",
                            "icon" => "",
                            "addon_icon" => null,
                            "selected" => false
                        ]
                    ]
                ]
            ];
            //底部导航
            $result = $diy_view_model->setBottomNavConfig(json_encode($diy_view_bottom_nav), $param[ 'site_id' ]);
            return $res;
        }
    }
}