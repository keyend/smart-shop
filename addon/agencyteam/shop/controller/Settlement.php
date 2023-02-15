<?php
namespace addon\agencyteam\shop\controller;

use app\Request;
use addon\agencyteam\model\AgencySettlementModel;
use addon\agencyteam\model\AgencyAccountLogModel;
use addon\agencyteam\Controller;

class Settlement extends Controller
{
    use \liliuwei\think\Jump;

    public function index()
    {
        header('Location: ' . addon_url('agencyteam://shop/settlement/lists'));
        exit();
    }

    public function lists(AgencySettlementModel $model)
    {
        if (request()->isAjax()) {
            $page      = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $condition = array();
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
                $condition[] = ['create_time|begin_time|last_time', 'between', [$start_time, $end_time]];
            } elseif (!$start_time && $end_time) {
                $condition[] = ['create_time|begin_time|last_time', '<=', $end_time];
            } elseif ($start_time && !$end_time) {
                $end_time = intval($end_time) + 86399;
                $condition[] = ['create_time|begin_time|last_time', '>=', $start_time];
            }
            return $model->getList($condition, $page, $page_size);
        } else {
            $signals = $model->getNoneSignal($this->site_id);
            $this->assign('signals', $signals);
            $this->fiveMenu();
            return $this->fetch('settlement/lists');
        }
    }

    public function last(AgencySettlementModel $model)
    {
        $signals = $model->getNoneSignal($this->site_id);
        $data = $signals['bonus']->toArray();
        if ($data != null) {
            if ($data['begin_time'] != 0) {
                $data['begin_time'] = date('Y-m-d H:i:s', $data['begin_time']);
            } else {
                $data['begin_time'] = '';
            }
        }

        return json([
            'code' => 0,
            'data' => $data
        ]);
    }

    public function commit(AgencySettlementModel $model)
    {
        try {
            $timestamp = input('last_time', '');
            if ($timestamp != '') {
                $timestamp = strtotime($timestamp);
            }

            $result = $model->settlement($this->site_id, $this->user_info['username'], $timestamp, 'manual');
        } catch(\Exception $e) {
            return json([
                'code' => $e->getCode(),
                'message' => '结算失败:' . $e->getMessage()
            ]);
        }

        return json([
            'code' => 0,
            'message' => '操作成功!',
            'data' => $result
        ]);
    }

    public function detail(AgencyAccountLogModel $model)
    {
        $id = (int)input('settlement_id', '');
        $filt      = input('filt', '');

        if (request()->isAjax() || $filt != '') {
            $page      = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $condition = array();
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
                $end_time = intval($end_time) + 86399;
                $condition[] = ['create_time', '>=', $start_time];
            }

            if ($id != 0) {
                $trade_no = app()->make(AgencySettlementModel::class)->where('id', $id)->value('trade_no');
                $condition[] = ['trade_no', '=', $trade_no];
            }

            if ($filt == 'obj.complex') {
                $page = 0;
                $page_size = 0;
            } elseif($filt == 'obj.export') {
                $page = 1;
                $page_size = 9998;
            }

            return $model->getList($condition, $page, $page_size);
        } else {
            $this->assign('settlement_id', $id);
            $this->fiveMenu(['id' => $id]);
            return $this->fetch('settlement/detail');
        }
    }
}