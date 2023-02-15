<?php
namespace addon\store\store\controller;
use app\model\system\User as UserModel;
use app\Controller;
use app\model\web\Config as ConfigModel;
use think\captcha\facade\Captcha as ThinkCaptcha;
use think\facade\Cache;
use app\model\system\Site;
/**
 * 登录
 * Class Login
 * @package app\shop\controller
 */
class Login extends Controller
{
	
	protected $replace = [];    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'
	protected $app_module = "store";
	protected $site_id;
	
	public function __construct()
	{
		//执行父类构造函数
		parent::__construct();
		$this->replace = [
			'STORE_CSS' => __ROOT__ . '/addon/store/store/view/public/css',
			'STORE_JS' => __ROOT__ . '/addon/store/store/view/public/js',
			'STORE_IMG' => __ROOT__ . '/addon/store/store/view/public/img',
		];
		//检测基础登录
		$this->site_id = request()->siteid();
		if (empty($this->site_id)) {
			$this->site_id = input("site_id", 0);
			request()->siteid($this->site_id);
		}
		if (empty($this->site_id)) {
			$this->redirect(url("shop/login/login"));
		}
	}
	
	/**
	 * 登录首页
	 * @return mixed
	 */
	public function login()
	{
		$config_model = new ConfigModel();
		$config_info = $config_model->getCaptchaConfig();
		$config = $config_info['data']['value'];
		$this->assign('shop_login', $config['shop_login']);
		if (request()->isAjax()) {
			$username = input("username", '');
			$password = input("password", '');
			$user_model = new UserModel();
			if ($config["shop_login"] == 1) {
				$captcha_result = $this->checkCaptcha();
				//验证码
				if ($captcha_result["code"] != 0) {
					return $captcha_result;
				}
			}
			$result = $user_model->login($username, $password, $this->app_module, $this->site_id);
			return $result;
		} else {
			$this->assign("menu_info", [ 'title' => "登录" ]);
			$this->assign("store_info", [ 'site_name' => "门店管理" ]);
			//平台配置信息
            $site_model = new Site();
            $site_info = $site_model->getSiteInfo([ ["site_id", "=", $this->site_id] ]);
			$this->assign('shop_info', $site_info['data']);
			$this->assign('domain', $_SERVER['SERVER_NAME']);
			$captcha = $this->captcha();
			$captcha = $captcha['data'];
			$this->assign("captcha", $captcha);
			//加载版权信息
			$copyright = $config_model->getCopyright();
			$this->assign('copyright', $copyright['data']['value']);
			
			return $this->fetch("login/login", [], $this->replace);
		}
		
	}
	
	/**
	 * 退出操作
	 */
	public function logout()
	{
		$user_model = new UserModel();
		$uid = $user_model->uid($this->app_module, $this->site_id);
		
		if ($uid > 0) {
			//清除登录信息session
			$user_model->clearLogin($this->app_module, $this->site_id);
			$this->redirect(url("store/store/login/login"));
		} else {
			$this->redirect(url("store/store/login/login"));
		}
	}
	
	/**
	 * 验证码
	 */
	public function captcha()
	{
		$captcha_data = ThinkCaptcha::create(null, true);
		$captcha_id = md5(uniqid(null, true));
		// 验证码10分钟有效
		Cache::set($captcha_id, $captcha_data['code'], 600);
		return success(0, '', [ 'id' => $captcha_id, 'img' => $captcha_data['img'] ]);
	}
	
	/**
	 * 验证码验证
	 */
	public function checkCaptcha()
	{
		$captcha = input('captcha', '');
		
		if (empty($captcha)) return error(-1, '请输入验证码');
		if (!captcha_check($captcha)) return error(-1, '验证码错误');
		
		return success();
	}
	
	/**
	 * 修改密码
	 * */
	public function modifyPassword()
	{
		if (request()->isAjax()) {
			$user_model = new UserModel();
			$uid = $user_model->uid($this->app_module, $this->site_id);
			
			$old_pass = input('old_pass', '');
			$new_pass = input('new_pass', '123456');
			
			$condition = [
				[ 'uid', '=', $uid ],
				[ 'password', '=', data_md5($old_pass) ],
				[ 'site_id', '=', $this->site_id ],
				[ 'app_module', '=', $this->app_module ]
			];
			
			$res = $user_model->modifyAdminUserPassword($condition, $new_pass);
			
			return $res;
		}
	}
	
	/**
	 * 清理缓存
	 */
	public function clearCache()
	{
		Cache::clear();
		return success('', '缓存更新成功', '');
	}
}