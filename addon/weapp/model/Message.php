<?php
namespace addon\weapp\model;
use app\model\BaseModel;
/**
 * 微信小程序订阅消息
 */
class Message extends BaseModel
{
    /**
     * 消息分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return \multitype
     */
    public function getMessagePageList($condition = [], $site_id = 0, $page = 1, $page_size = PAGE_LIST_ROWS, $order = '')
    {
        $list = model('message_template')->pageList($condition, 'id,keywords,title,message_type,weapp_json', $order, $page, $page_size);
        if ($site_id > 0) {
            if (!empty($list[ 'list' ])) {
                foreach ($list[ 'list' ] as $k => $v) {
                    $list[ 'list' ][ $k ]['message_info'] = json_decode($v['weapp_json'], true);
                    $message_info = model('message')->getInfo([ [ "keywords", "=", $v[ 'keywords' ] ], [ 'site_id', '=', $site_id ] ], 'weapp_is_open,weapp_template_id');
                    $list[ 'list' ][ $k ][ 'weapp_is_open' ] = $message_info == null ? 0 : $message_info[ 'weapp_is_open' ];
                    $list[ 'list' ][ $k ][ 'weapp_template_id' ] = $message_info == null ? 0 : $message_info[ 'weapp_template_id' ];
                }
            }
        }
        return $this->success($list);
    }
    /**
     * 获取微信模板消息id
     * @param string $keywords
     * todo 批量获取模板消息
     */
    public function getWeappTemplateNo(string $keywords, $site_id, $weapp_is_open = 0)
    {
        $keyword = explode(',', $keywords);
        $wechat = new Weapp($site_id);
        if ($weapp_is_open == 1) {
            // 启用
            foreach ($keyword as $item) {
                $shop_message = model('message')->getInfo([ [ 'keywords', '=', $item ], [ "site_id", "=", $site_id ] ], 'weapp_template_id');
                $data = [
                    'weapp_is_open' => $weapp_is_open,
                    'site_id' => $site_id,
                    'keywords' => $item,
                ];
                // 开启时没有模板则进行添加
                if (!empty($shop_message)) {
                    if (empty($shop_message[ 'weapp_template_id' ])) {
                        $template_info = model('message_template')->getInfo([ [ 'keywords', '=', $item ], [ 'weapp_json', '<>', '' ] ], 'weapp_json');
                        if (!empty($template_info)) {
                            $template = json_decode($template_info[ 'weapp_json' ], true);
                            $res = $wechat->getTemplateId($template);
                            if (isset($res[ 'errcode' ]) && $res[ 'errcode' ] == 0) {
                                $data[ 'weapp_template_id' ] = $res[ 'priTmplId' ];
                            } else {
                                return $this->error($res, $res[ 'errmsg' ]);
                            }
                        }
                    }
                    model('message')->update($data, [ [ 'keywords', '=', $item ], [ "site_id", "=", $site_id ] ]);
                } else {
                    $template_info = model('message_template')->getInfo([ [ 'keywords', '=', $item ], [ 'weapp_json', '<>', '' ] ], 'weapp_json');
                    if (!empty($template_info)) {
                        $template = json_decode($template_info[ 'weapp_json' ], true);
                        $res = $wechat->getTemplateId($template);
                        if (isset($res[ 'errcode' ]) && $res[ 'errcode' ] == 0) {
                            $data[ 'weapp_template_id' ] = $res[ 'priTmplId' ];
                        } else {
                            return $this->error($res, $res[ 'errmsg' ]);
                        }
                    }
                    model('message')->add($data);
                }
            }
        } else if ($weapp_is_open == 0) {
            // 关闭
            foreach ($keyword as $item) {
                $shop_message = model('message')->getInfo([ [ 'keywords', '=', $item ], [ "site_id", "=", $site_id ] ], 'weapp_template_id');
                if (!empty($shop_message)) {
                    model('message')->update([ 'weapp_is_open' => $weapp_is_open ], [ [ 'keywords', '=', $item ], [ "site_id", "=", $site_id ] ]);
                } else {
                    model('message')->add([
                        'site_id' => $site_id,
                        'keywords' => $item,
                        'weapp_is_open' => $weapp_is_open
                    ]);
                }
            }
        } else {
            // 获取
            $list = model('message_template')->getList([ [ 'keywords', 'in', $keyword ], [ 'weapp_json', '<>', '' ] ], 'keywords,weapp_json');
            if (!empty($list)) {
                foreach ($list as $item) {
                    $template = json_decode($item[ 'weapp_json' ], true);
                    $res = $wechat->getTemplateId($template);
                    if (isset($res[ 'errcode' ]) && $res[ 'errcode' ] != 0) return $this->error($res, $res[ 'errmsg' ]);
                    $shop_message = model('message')->getInfo([ [ 'keywords', '=', $item[ 'keywords' ] ], [ "site_id", "=", $site_id ] ], 'weapp_template_id');
                    if (!empty($shop_message)) {
                        model('message')->update([ 'weapp_template_id' => $res[ 'priTmplId' ] ], [ [ 'keywords', '=', $item[ 'keywords' ] ], [ "site_id", "=", $site_id ] ]);
                    } else {
                        model('message')->add([
                            'site_id' => $site_id,
                            'keywords' => $item[ 'keywords' ],
                            'weapp_template_id' => $res[ 'priTmplId' ]
                        ]);
                    }
                }
            }
        }
        return $this->success();
    }
    /**
     * 发送订阅消息
     * @param array $param
     */
    public function sendMessage(array $param)
    {
        try {
            $site_id = $param['site_id'] ? $param['site_id'] : 1;
            $support_type = $param['message_info']["support_type"] ?? [];
            if (empty($support_type) || strpos($support_type, "weapp") === false) return $this->success();
            if (empty($param['openid'])) return $this->success('缺少必需参数openid');
            $message_info = $param['message_info'];
            if ($message_info['weapp_is_open'] == 0) return $this->error('未启用模板消息');
            if (!isset($message_info['weapp_template_id']) || empty($message_info['weapp_template_id'])) return $this->error('未配置模板消息');
            $data = [
                'openid' => $param['openid'],
                'template_id' => $message_info['weapp_template_id'],
                'data' => $param['template_data'],
                'page' => $param['page'] ?? ''
            ];
            $weapp = new Weapp($site_id);
            $res = $weapp->sendTemplateMessage($data);
            return $res;
        } catch (\Exception $e) {
            return $this->error('', "消息发送失败");
        }
    }
    /**
     * 获取订阅消息模板id集合
     * @param $site_id
     * @param $keywords
     */
    public function getMessageTmplIds($site_id, $keywords){
        $data = model('message')->getColumn([ ['weapp_is_open', '=', 1], ['weapp_template_id', '<>', ''], ['site_id', '=', $site_id], ['keywords', 'in', explode(',', $keywords) ] ], 'weapp_template_id');
        return $this->success($data);
    }
}