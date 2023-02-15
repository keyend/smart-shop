<?php
namespace addon\agencyteam\shop\controller;

use app\Request;
use addon\agencyteam\model\AgencyTeamConfigModel as ConfigModel;
use addon\agencyteam\model\AgencyTeamLevelModel;
use addon\agencyteam\Controller;

class Level extends Controller
{
    public function lists()
    {
        $model = new AgencyTeamLevelModel();
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
            return $this->fetch('level/lists');
        }
    }
    /**
     * 添加分销等级
     */
    public function add(Request $request)
    {
        $model = app()->make(AgencyTeamLevelModel::class);
        if (request()->isAjax()) {
            $data = $request->post();
            $data['site_id'] = $this->site_id;
            $data['rate_bonus'] = (float)$data['rate_bonus'];
            $data['rate_subsidy'] = (float)$data['rate_subsidy'];
            $data['subsidy_condition']['usertotal'] = (int)$data['subsidy_condition']['usertotal'];
            $data['subsidy_condition']['achievement'] = (int)$data['subsidy_condition']['achievement'];
            $data['subsidy_condition']['children'] = (int)$data['subsidy_condition']['children'];
            $data['subsidy_condition'] = json_encode($data['subsidy_condition']);
            $data['condition']['action'] = (int)$data['condition']['action'];
            $data['condition']['days'] = (int)$data['condition']['days'];
            $data['condition']['people'] = (int)$data['condition']['people'];
            $data['condition'] = json_encode($data['condition']);
            $res  = $model->addLevel($data);
            return $res;
        } else {
            //获取系统配置
            $config_model = app()->make(ConfigModel::class);
            $basics       = $config_model->getBasicConfig($this->site_id);
            $this->assign("basics_info", $basics['data']['value']);
            $level_weight = $model->getLevelList([ ['level', '<>', ''],['site_id', '=', $this->site_id] ], 'level');
            $level_weight = $level_weight['data'];
            if (!empty($level_weight)) $level_weight = array_column($level_weight, 'level');
            $this->assign('level_weight', $level_weight);
            return $this->fetch('level/add');
        }
    }
    /**
     * 编辑分销等级
     */
    public function edit(Request $request)
    {
        $model = app()->make(AgencyTeamLevelModel::class);
        if (request()->isAjax()) {
            $data = $request->post();
            $data['rate_bonus'] = (float)$data['rate_bonus'];
            $data['rate_subsidy'] = (float)$data['rate_subsidy'];
            $data['subsidy_condition']['usertotal'] = (int)$data['subsidy_condition']['usertotal'];
            $data['subsidy_condition']['achievement'] = (int)$data['subsidy_condition']['achievement'];
            $data['subsidy_condition']['children'] = (int)$data['subsidy_condition']['children'];
            $data['subsidy_condition'] = json_encode($data['subsidy_condition']);
            $data['condition']['action'] = (int)$data['condition']['action'];
            $data['condition']['days'] = (int)$data['condition']['days'];
            $data['condition']['people'] = (int)$data['condition']['people'];
            $data['condition'] = json_encode($data['condition']);
            $level_id = input('id', '');
            $res = $model->editLevel($data, [['id', '=', $level_id], ['site_id', '=', $this->site_id]]);
            return $res;
        } else {
            $level_id = input('id', '');
            $info     = $model->getLevelInfo([['id', '=', $level_id]]);
            $this->assign('info', $info['data']);
            //获取系统配置
            $config_model = new ConfigModel();
            $basics       = $config_model->getBasicConfig($this->site_id);
            $this->assign("basics_info", $basics['data']['value']);
            $level_weight = $model->getLevelList([ ['level', '<>', ''],['id', '<>', $level_id],['site_id', '=', $this->site_id] ], 'level');
            $level_weight = $level_weight['data'];
            if (!empty($level_weight)) $level_weight = array_column($level_weight, 'level');
            $this->assign('level_weight', $level_weight);
        }
        return $this->fetch('level/edit');
    }
    /**
     * 删除分销等级
     */
    public function delete()
    {
        $model = app()->make(AgencyTeamLevelModel::class);
        $level_id = input('level_id', '');
        $res      = $model->deleteLevel($level_id, $this->site_id);
        return $res;
    }
}