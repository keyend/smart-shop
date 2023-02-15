<?php
namespace addon\agencyteam;
use think\facade\Cache;

class Model extends \think\Model
{
    /**
     * 外用模型
     * @var object
     */
    protected $_model;

    /**
     * 语言包
     * @var object
     */
    protected $lang;

    /**
     * 架构函数
     * @access public
     * @param array $data 数据
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->__init();
    }

    protected function __init()
    {}

    /**
     * 操作成功返回值函数
     * @param string $data
     * @param string $code_var
     * @return array
     */
    protected function success($data = '', $code_var = 'SUCCESS')
    {
        $lang_array = $this->getLang();
        $lang_var   = isset($lang_array[$code_var]) ? $lang_array[$code_var] : $code_var;
        if ($code_var == 'SUCCESS') {
            $code_var = 0;
        } else {
            $code_array = array_keys($lang_array);
            $code_index = array_search($code_var, $code_array);
            if ($code_index != false) {
                $code_var = 10000 + $code_index;
            }
        }

        return success($code_var, $lang_var, $data);
    }
    /**
     * 操作失败返回值函数
     * @param string $data
     * @param string $code_var
     * @return array
     */
    protected function error($data = '', $code_var = 'FAIL')
    {
        $lang_array = $this->getLang();
        if (isset($lang_array[$code_var])) {
            $lang_var = $lang_array[$code_var];
        } else {
            $lang_var = $code_var;
            $code_var = 'FAIL';
        }
        $code_array = array_keys($lang_array);
        $code_index = array_search($code_var, $code_array);
        if ($code_index != false) {
            $code_var = -10000 - $code_index;
        }

        return error($code_var, $lang_var, $data);
    }
    /**
     * 获取语言包数组
     * @return Ambigous <multitype:, unknown>
     */
    protected function getLang()
    {
        $default_lang = config("lang.default_lang");
        $cache_common = Cache::get("lang_app/lang/" . $default_lang . '/model.php');
        if (empty($cache_common)) {
            $cache_common = include 'app/lang/' . $default_lang . '/model.php';
            Cache::tag("lang")->set("lang_app/lang/" . $default_lang, $cache_common);
        }
        $lang_path = isset($this->lang) ? $this->lang : '';
        if (!empty($lang_path)) {
            $cache_path = Cache::get("lang_" . $lang_path . "/" . $default_lang . '/model.php');
            if (empty($cache_path)) {
                $cache_path = include $lang_path . "/" . $default_lang . '/model.php';
                Cache::tag("lang")->set("lang_" . $lang_path . "/" . $default_lang, $cache_path);
            }
            $lang = array_merge($cache_common, $cache_path);
        } else {
            $lang = $cache_common;
        }

        return $lang;
    }
}