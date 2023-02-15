<?php
namespace addon\agencyteam\model;
use addon\agencyteam\Model;
use app\model\system\Config as ConfigModel;

class AgencyTeamAreaModel extends Model
{
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'agency_area';

    /**
     * 主键名称
     * @var string
     */
    protected $pk = 'id';

    protected function __init()
    {
        $this->_model = model('agency_area');
    }

    /**
     * 获取记录分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        return $this->success($this->_model->pageList($condition, $field, $order, $page, $page_size));
    }

    /**
     * 获取等级列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getLevelList($condition = [], $field = '*', $order = '', $limit = null)
    {
        return $this->success($this->_model->getList($condition, $field, $order, '', '', '', $limit));
    }

    /**
     * 获取记录信息
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getInfo($condition = [], $field = '*')
    {
        $data = $this->_model->getInfo($condition, $field);

        return $this->success($data);
    }

    public function add($data)
    {
        $data['create_time'] = time();
        $data['status']      = 1;
        $this->insert($data);

        return $this->success($this->getLastInsId());
    }

    public function edit($data)
    {
        $data['status']      = 1;
        $this->update($data);

        return $this->success();
    }
}