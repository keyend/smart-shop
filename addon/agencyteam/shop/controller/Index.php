<?php
namespace addon\agencyteam\shop\controller;

use app\Request;
use app\model\goods\Goods as GoodsModel;
use app\model\member\Member;
use app\model\system\Address as AddressModel;
use addon\agencyteam\model\AgencyTeamLevelModel;
use addon\agencyteam\model\AgencyTeamAreaModel;
use addon\agencyteam\model\AgencyAccountModel;
use addon\agencyteam\model\AgencyTeamModel;
use addon\agencyteam\model\AgencyAccountLogModel;
use addon\agencyteam\model\AgencyTeamOrderModel;
use addon\agencyteam\Controller;

class Index extends Controller
{
    public function index()
    {
        header('Location: ' . addon_url('agencyteam://shop/index/dashboard'));
        exit();
    }

    /**
     * 近一年业绩
     *
     * @return void
     */
    private function getWeek52()
    {
        $lastTime = time();
        $beginTime = strtotime('-52 week', $lastTime);
        $totalMoney = (float) app()->make(AgencyTeamOrderModel::class)->where('finish_time', 'BETWEEN', [$beginTime, $lastTime])->sum('order_money');
        return $totalMoney;
    }

    /**
     * 获取总业绩
     *
     * @return void
     */
    private function getTotalPerformance()
    {
        $totalMoney = (float) app()->make(AgencyTeamOrderModel::class)->sum('order_money');
        return $totalMoney;
    }

    /**
     * 团队人数
     *
     * @return void
     */
    private function getTotalTeamCount()
    {
        $totalCount = (int) app()->make(AgencyTeamModel::class)->where('status', 'IN', [0,2])->count();
        return $totalCount;
    }

    public function dashboard()
    {
        $total_money = $this->getTotalTeamCount();
        $total_account = $this->getTotalPerformance();
        $w52_money = $this->getWeek52();

        $this->assign('total_account', $total_account);
        $this->assign('total_money', $total_money);
        $this->assign('w52_money', $w52_money);

        return $this->fetch('index/dashboard');
    }

    public function lists()
    {
        $model = new AgencyTeamModel();
        $field = 'um.username,um.nickname,um.member_id as user_id,ag.*';

        if (request()->isAjax()) {
            $page      = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $condition = array();
            $name = input('keyword', '');
            if ($name) {
                $condition[] = ['um.nickname|um.username', 'like', '%' . $name . '%'];
            }
            $level_id = input('level_id', '');
            if ($level_id) {
                $condition[] = ['ag.level_id', '=', $level_id];
            }
            $status = input('status', '');
            if (!empty($status)) {
                $condition[] = ['ag.status', '=', $status];
            }
            $start_time = input('start_time', '');
            $end_time = input('end_time', '');
            if ($start_time && !is_numeric($start_time)) {
                $start_time = date_to_time($start_time);
            }
            if ($end_time && !is_numeric($end_time)) {
                $end_time = date_to_time($end_time);
            }
            if ($start_time && $end_time) {
                $end_time = intval($end_time) + 86399;
                $condition[] = ['ag.create_time', 'between', [$start_time, $end_time]];
            } elseif (!$start_time && $end_time) {
                $condition[] = ['ag.create_time', '<=', $end_time];
            } elseif ($start_time && !$end_time) {
                $end_time = intval($end_time) + 86399;
                $condition[] = ['ag.create_time', '>=', $start_time];
            }

            $list = $model->getList($condition, $page, $page_size, 'um.id DESC', $field);
            return $list;
        } else {
            $level_list = app()->make(AgencyTeamLevelModel::class)->getLevelList([['status', '=', 1], ['site_id', '=', $this->site_id]], 'level,levelname');
            $area_list = app()->make(AgencyTeamAreaModel::class)->getLevelList([['status', '=', 1], ['site_id', '=', $this->site_id]], 'id,areaname');
            $province_list = app()->make(AddressModel::class)->getAreaList([["pid", "=", 0], ["level", "=", 1]]);
            $this->assign("province_list", $province_list["data"]);
            $this->assign('level_list', $level_list['data']);
            $this->assign('area_list', $area_list['data']);
            return $this->fetch('index/lists');
        }
    }

    public function detail(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->post();
            if ($data['level'] == 0) {
                $data['levelname'] = '';
            }
            if ($data['area_id'] == 0) {
                $data['areaname'] = '';
                $data['province_id'] = 0;
                $data['city_id'] = 0;
                $data['district_id'] = 0;
                $data['full_address'] = '';
            }
            $res = app()->make(AgencyTeamModel::class)->edit($data);
            return $res;
        } else {
            $id = input('agencyteam_id', '');
            $fields = 'um.username,um.nickname,um.member_id as user_id,ag.*';
            $info = app()->make(AgencyTeamModel::class)->getInfo($id, $fields);
            $level_list = app()->make(AgencyTeamLevelModel::class)->getLevelList([['status', '=', 1], ['site_id', '=', $this->site_id]], 'level,levelname');
            $province_list = app()->make(AddressModel::class)->getAreaList([["pid", "=", 0], ["level", "=", 1]]);
            $area_list = app()->make(AgencyTeamAreaModel::class)->getLevelList([['status', '=', 1], ['site_id', '=', $this->site_id]], 'id,areaname');
            $account_list = app()->make(AgencyAccountModel::class)->getTeamSettlementAccount($info['member_id']);
            $status = [
                '',
                '未开通', 
                '正常',
                '锁定'
            ];

            $this->assign('account_list', $account_list);
            $this->assign("province_list", $province_list["data"]);
            $this->assign('level_list', $level_list['data']);
            $this->assign('area_list', $area_list['data']);
            $this->assign('info', $info);
            $this->assign('status', $status);
            $this->fiveMenu(['agencyteam_id' => $id]);
    
            return $this->fetch('index/detail');
        }
    }

    public function team(Request $request)
    {
        $model = app()->make(AgencyTeamModel::class);
        $id = (int)input('agencyteam_id', '');
        $fields = 'um.username,um.nickname,um.member_id as user_id,ag.*';
        $info = $model->getInfo($id, $fields);

        if ($request->isAjax()) {
            $level = input('level', 0);
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $list = $model->getTeamList($info, $page, $page_size);
            return $list;
        } else {
            $this->assign('info', $info);
            $this->assign('agencyteam_id', $id);
            $this->fiveMenu(['agencyteam_id' => $id]);

            return $this->fetch('index/team');
        }
    }

    public function account(Request $request)
    {
        $model = app()->make(AgencyTeamModel::class);
        $id = (int)input('agencyteam_id', '');
        $fields = 'um.username,um.nickname,um.member_id as user_id,ag.*';
        $info = $model->getInfo($id, $fields);

        if ($request->isAjax()) {
            $level = input('level', 0);
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $condition = [];
            $condition[] = ['member_id', '=', $info['member_id']];
            $start_time = input('start_time', '');
            $end_time = input('end_time', '');
            if ($start_time && !is_numeric($start_time)) {
                $start_time = date_to_time($start_time);
            }
            if ($end_time && !is_numeric($end_time)) {
                $end_time = date_to_time($end_time);
            }
            if ($start_time && $end_time) {
                $end_time = intval($end_time) + 86399;
                $condition[] = ['create_time', 'between', [$start_time, $end_time]];
            } elseif (!$start_time && $end_time) {
                $condition[] = ['create_time', '<=', $end_time];
            } elseif ($start_time && !$end_time) {
                $condition[] = ['create_time', '>=', $start_time];
            }
            $list = app()->make(AgencyAccountLogModel::class)->getList($condition, $page, $page_size);
            return $list;
        } else {
            $account_list = app()->make(AgencyAccountModel::class)->getTeamSettlementAccount($info['member_id']);
            $account_list = array_column($account_list, 'value', 'name');

            $this->assign('info', $info);
            $this->assign('agencyteam_id', $id);
            $this->assign('account_list', $account_list);
            $this->fiveMenu(['agencyteam_id' => $id]);

            return $this->fetch('index/account');
        }
    }

    public function frozen(AgencyTeamModel $model, Request $req)
    {
        if ($req->isAjax()) {
            $member_id = (int)input('agencyteam_id', 0);
            return $model->setStatus($member_id, 0);
        }
    }

    public function unfrozen(AgencyTeamModel $model, Request $req)
    {
        if ($req->isAjax()) {
            $member_id = (int)input('agencyteam_id', 0);
            return $model->setStatus($member_id, 2);
        }
    }
}