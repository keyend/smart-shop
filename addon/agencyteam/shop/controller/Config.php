<?php
namespace addon\agencyteam\shop\controller;

use app\Request;
use addon\agencyteam\model\AgencyTeamConfigModel as ConfigModel;
use app\model\system\Document;
use addon\agencyteam\Controller;

class Config extends Controller
{
    public function index()
    {
        header('Location: ' . addon_url('agencyteam://shop/config/basic'));
        exit();
    }

    public function basic(Request $request)
    {
        $model = new ConfigModel();
        if (request()->isAjax()) {
            $data = $request->post();
            $res = null;

            foreach($data as $key => $value) {
                if (is_array($value)) {
                    $res = $model->setValues(strtoupper($key), $value, $this->site_id);
                }
            }

            return $res;
        } else {
            // 基础设置
            $basics = $model->getBasicConfig($this->site_id);
            $this->assign("basic", $basics['data']['value']);
            // 奖金分配
            $split = $model->getSplitConfig($this->site_id);
            $this->assign("split", $split['data']['value']);
            // 升级条件
            $upgrade = $model->getUpgradeConfig($this->site_id);
            $this->assign("upgrade", $upgrade['data']['value']);

            $this->forthMenu();
            return $this->fetch('config/basic');
        }
    }

    public function settlement(Request $request)
    {
        $model = new ConfigModel();
        if (request()->isAjax()) {
            $data = $request->post();
            $res = null;

            foreach($data as $key => $value) {
                if (is_array($value)) {
                    $res = $model->setValues(strtoupper($key), $value, $this->site_id);
                }
            }

            return $res;
        } else {
            $settlement = $model->getSettlementConfig($this->site_id);
            $this->assign("settlement", $settlement['data']['value']);
            $this->forthMenu();
            return $this->fetch('config/settlement');
        }
    }

    public function agreement(Request $request)
    {
        $model = new ConfigModel();
        if (request()->isAjax()) {
            $data = $request->post();
            $res = null;

            foreach($data as $key => $value) {
                if (is_array($value)) {
                    $res = $model->setValues(strtoupper($key), $value, $this->site_id);
                }
            }

            return $res;
        } else {
            $agreement = $model->getAgreementConfig($this->site_id);
            $this->assign("agreement", $agreement['data']['value']);
            $this->forthMenu();
            return $this->fetch('config/agreement');
        }
    }

    public function words(Request $request)
    {
        $model = new ConfigModel();
        if (request()->isAjax()) {
            $data = $request->post();
            $res = null;

            foreach($data as $key => $value) {
                if (is_array($value)) {
                    $res = $model->setValues(strtoupper($key), $value, $this->site_id);
                }
            }

            return $res;
        } else {
            $agreement = $model->getWordsConfig($this->site_id);
            $this->assign("words", $agreement['data']['value']);
            $this->forthMenu();
            return $this->fetch('config/words');
        }
    }
}