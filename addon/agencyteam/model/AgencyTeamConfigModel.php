<?php
namespace addon\agencyteam\model;
use addon\agencyteam\Model;
use app\model\system\Config as ConfigModel;

class AgencyTeamConfigModel extends Model
{
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'config';

    /**
     * 主键名称
     * @var string
     */
    protected $pk = 'id';

    private $names = [
        'BASIC' => '团队基础配置',
        'SPLIT' => '团队分配策略',
        'UPGRADE' => '团队升级条件',
        'SETTLEMENT' => '团队结算',
        'AGREEMENT' => '团队申请协议',
        'WORDS' => '团队文字设置'
    ];

    protected function __init()
    {
        $this->_model = app()->make(ConfigModel::class);
    }

    public function getValues($site_id, $name = '')
    {
        return $this->_model->getConfig([['site_id', '=', $site_id], ['app_module', '=', 'shop'], ['config_key', '=', 'AGENCYTEAM_' . $name . '_CONFIG']]);
    }

    public function setValues($name, $value, $site_id)
    {
        if (isset($this->names[$name])) {
            return $this->_model->setConfig($value, $this->names[$name], 1, [['site_id', '=', $site_id], ['app_module', '=', 'shop'], ['config_key', '=', 'AGENCYTEAM_' . $name . '_CONFIG']]);
        }
    }

    public function getBasicConfig($site_id)
    {
        $res = $this->getValues($site_id, 'BASIC');
        if (empty($res['data']['value'])) {
            $res['data']['value'] = [
                'status'       => 0,  // 开启状态
            ];
        }

        return $res;
    }

    public function getSplitConfig($site_id)
    {
        $res = $this->getValues($site_id, 'SPLIT');
        if (empty($res['data']['value'])) {
            $res['data']['value'] = [
                'status'       => 0,
                'total'        => 0,
                'static'       => [], // 静态分配
                'dynamic'      => [], // 动态分配
            ];
        }

        return $res;
    }

    public function getUpgradeConfig($site_id)
    {
        $res = $this->getValues($site_id, 'UPGRADE');
        if (empty($res['data']['value'])) {
            $res['data']['value'] = [
                'condition'     => 0,
            ];
        }

        return $res;
    }

    public function getSettlementConfig($site_id)
    {
        $res = $this->getValues($site_id, 'SETTLEMENT');
        if (empty($res['data']['value'])) {
            $res['data']['value'] = [
                'type'            => 0,
                'cycle_mix'       => 0, // 结算最小周期时间(天)
            ];
        }

        return $res;
    }

    public function getAgreementConfig($site_id)
    {
        $res = $this->getValues($site_id, 'AGREEMENT');
        if (empty($res['data']['value'])) {
            $res['data']['value'] = [
                'img'           => '', // 顶部图片
                'is_show'       => 0, // 是否显示协议
                'title'         => '',
                'content'       => ''
            ];
        }

        return $res;
    }

    public function getWordsConfig($site_id)
    {
        $res = $this->getValues($site_id, 'WORDS');
        if (empty($res['data']['value'])) {
            $res['data']['value'] = [
                'name' => '',
                'point' => '',
                'join' => '',
                'bonus' => '',
                'bonus_total' => '',
                'bonus_lock' => '',
                'bonus_pay' => '',
                'bonus_wait' => '',
                'bonus_detail' => '',
            ];
        }

        return $res;
    }
}