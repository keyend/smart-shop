<?php
namespace addon\agencyteam\shop\controller;

use app\Request;
use addon\agencyteam\model\AgencyTeamConfigModel as ConfigModel;
use addon\agencyteam\model\AgencyTeamAreaModel;
use addon\agencyteam\Controller;

class Province extends Controller
{
    public function index()
    {
        header('Location: ' . addon_url('agencyteam://shop/province/lists'));
        exit();
    }

    public function lists()
    {
        $model = new AgencyTeamAreaModel();
        $field = '*';

        if (request()->isAjax()) {
            $page      = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $list      = $model->getList([['site_id', '=', $this->site_id]], $page, $page_size, 'sort DESC', $field);
            return $list;
        } else {
            //获取系统配置
            $config_model = new ConfigModel();
            $basics       = $config_model->getBasicConfig($this->site_id);
            $this->assign("basics_info", $basics['data']['value']);
            return $this->fetch('province/lists');
        }
    }

    public function add(Request $request)
    {
        $model = app()->make(AgencyTeamAreaModel::class);
        if (request()->isAjax()) {
            $data = $request->post();
            $data['site_id'] = $this->site_id;
            $data['rate_bonus'] = (float)$data['rate_bonus'];
            $res  = $model->add($data);
            return $res;
        } else {
            //获取系统配置
            $config_model = app()->make(ConfigModel::class);
            $basics       = $config_model->getBasicConfig($this->site_id);
            $this->assign("basics_info", $basics['data']['value']);
            return $this->fetch('province/add');
        }
    }

    public function edit(Request $request)
    {
        $model = app()->make(AgencyTeamAreaModel::class);
        if (request()->isAjax()) {
            $data = $request->post();
            $data['site_id'] = $this->site_id;
            $data['rate_bonus'] = (float)$data['rate_bonus'];
            $res  = $model->edit($data);
            return $res;
        } else {
            $level_id = input('id', '');
            $info     = $model->getInfo([['id', '=', $level_id]]);
            $this->assign('info', $info['data']);
            //获取系统配置
            $config_model = app()->make(ConfigModel::class);
            $basics       = $config_model->getBasicConfig($this->site_id);
            $this->assign("basics_info", $basics['data']['value']);
            return $this->fetch('province/edit');
        }
    }
}