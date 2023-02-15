<?php
namespace addon\agencyteam\model;
use addon\agencyteam\Model;
use app\model\system\Config as ConfigModel;

class AgencyMemberModel extends Model
{
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'member';

    /**
     * 主键名称
     * @var string
     */
    protected $pk = 'member_id';
}